#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <HTTPClient.h>
#include "mbedtls/gcm.h"
#include "mbedtls/base64.h"
#include "mbedtls/md.h" 
#include <time.h> 
#include <vector>       

const char* ssid               = "oplus_co_appvun";
const char* password           = "lahh1153";
const char* mqtt_server        = "103.127.132.111";
const int   mqtt_port          = 1883;
const char* mqtt_topic_control = "smartsystem/control"; 

const char* BOARD_UID          = "ESP32_GATEWAY_1"; 
const char* config_api_url     = "https://smartdoor.ahmadtechsolusindo.id/api/devices/get_board_config?board_uid=ESP32_GATEWAY_1";
const char* sync_api_url       = "https://smartdoor.ahmadtechsolusindo.id/api/devices/sync_state"; 

const unsigned char* aes_key   = (const unsigned char*)"4fE9xR2wL7pM1nQ8vB5zY3tS6hG0kC4j";
const char* signature_key      = "9zY3tS6hG0kC4j4fE9xR2wL7pM1nQ8vB"; 

struct SmartDevice {
  int id_device;
  int pin_gpio;
  int active_state;
  int last_state; 
};

std::vector<SmartDevice> myDevices; 
WiFiClient espClient;
PubSubClient client(espClient);

void setup_wifi() {
  delay(10);
  Serial.print("\nConnecting to "); Serial.println(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) { delay(500); Serial.print("."); }
  Serial.println("\nWiFi Connected!");
}

void setup_ntp() {
  configTime(0, 0, "pool.ntp.org", "time.nist.gov");
  Serial.print("Sinkronisasi Waktu NTP...");
  time_t now = time(nullptr);
  while (now < 24 * 3600) { delay(500); Serial.print("."); now = time(nullptr); }
  Serial.println("\nWaktu Sinkron!");
}

void fetchDynamicConfiguration() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(config_api_url);
    int httpResponseCode = http.GET();
    
    if (httpResponseCode == 200) {
      String payload = http.getString();
      DynamicJsonDocument doc(2048);
      deserializeJson(doc, payload);
      
      if (doc["success"] == true) {
        JsonArray devicesArray = doc["devices"].as<JsonArray>();
        myDevices.clear();
        
        for (JsonObject devObj : devicesArray) {
          SmartDevice dev;
          dev.id_device    = devObj["id"];
          dev.pin_gpio     = devObj["pin"];
          dev.active_state = devObj["state"];
          dev.last_state   = devObj["last_state"]; 
          myDevices.push_back(dev);
          
          pinMode(dev.pin_gpio, OUTPUT);
          
          if (dev.last_state == 1) {
            digitalWrite(dev.pin_gpio, (dev.active_state == 1) ? HIGH : LOW);
          } else {
            digitalWrite(dev.pin_gpio, (dev.active_state == 1) ? LOW : HIGH);
          }
          
          Serial.printf("[Provisioning] ID %d dialokasikan ke PIN %d (Last State DB: %d)\n", dev.id_device, dev.pin_gpio, dev.last_state);
        }
      }
    }
    http.end();
  }
}

void updateStateToDatabase(int id_device, int actual_state) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(sync_api_url) + "?id=" + String(id_device) + "&state=" + String(actual_state);
    
    http.begin(url);
    int httpResponseCode = http.GET(); 
    
    if (httpResponseCode == 200) {
      Serial.printf("[Feedback] Konfirmasi sukses sinkronisasi perangkat %d ke kondisi %d.\n", id_device, actual_state);
    } else {
      Serial.printf("[Feedback Error] Gagal sinkronisasi DB, kode HTTP: %d\n", httpResponseCode);
    }
    http.end();
  }
}

String generateHMAC(String payload) {
  byte hmacResult[32];
  mbedtls_md_context_t ctx;
  mbedtls_md_init(&ctx);
  mbedtls_md_setup(&ctx, mbedtls_md_info_from_type(MBEDTLS_MD_SHA256), 1);
  mbedtls_md_hmac_starts(&ctx, (const unsigned char *) signature_key, strlen(signature_key));
  mbedtls_md_hmac_update(&ctx, (const unsigned char *) payload.c_str(), payload.length());
  mbedtls_md_hmac_finish(&ctx, hmacResult);
  mbedtls_md_free(&ctx);

  String hash = "";
  for(int i = 0; i < 32; i++) { char str[3]; sprintf(str, "%02x", (int)hmacResult[i]); hash += str; }
  return hash;
}

bool decrypt_aes_gcm(const unsigned char* nonce, size_t nonce_len, const unsigned char* ciphertext, size_t ciphertext_len, const unsigned char* tag, size_t tag_len, unsigned char* plaintext) {
  mbedtls_gcm_context ctx;
  mbedtls_gcm_init(&ctx);
  int ret = mbedtls_gcm_setkey(&ctx, MBEDTLS_CIPHER_ID_AES, aes_key, 256);
  if (ret != 0) { mbedtls_gcm_free(&ctx); return false; }
  ret = mbedtls_gcm_auth_decrypt(&ctx, ciphertext_len, nonce, nonce_len, NULL, 0, tag, tag_len, ciphertext, plaintext);
  mbedtls_gcm_free(&ctx);
  return (ret == 0);
}

void callback(char* topic, byte* payload, unsigned int length) {
  // --- MULAI PENGUKURAN KINERJA ---
  unsigned long t_start = millis(); // Catat waktu mulai (milidetik)
  uint32_t heap_before = ESP.getFreeHeap(); // Catat sisa RAM sebelum proses
  
  Serial.println("\n========== PESAN MQTT MASUK ==========");
  Serial.printf("[METRIK] Free Heap Awal: %d Bytes\n", heap_before);

  String message;
  for (int i = 0; i < length; i++) {
    message += (char)payload[i];
  }
  
  DynamicJsonDocument doc(1024);
  DeserializationError error = deserializeJson(doc, message);
  if (error) {
    Serial.println("[ERROR] Gagal parsing JSON MQTT");
    return;
  }

  String nonce_base64 = doc["nonce"];
  String ct_base64 = doc["ciphertext"];
  String tag_base64 = doc["tag"];
  String received_sig = doc["signature"];

  // 1. Validasi HMAC (Integritas)
  String data_to_sign = nonce_base64 + "." + ct_base64 + "." + tag_base64;
  if (generateHMAC(data_to_sign) != received_sig) {
    Serial.println("[SECURITY ALERT] Signature HMAC Tidak Valid! Paket Dibuang.");
    return; 
  }
  
  // 2. Persiapan Dekripsi (Decode Base64)
  unsigned char nonce_bytes[16], ciphertext_bytes[256], tag_bytes[16];
  size_t nonce_len, ciphertext_len, tag_len;
  
  mbedtls_base64_decode(nonce_bytes, sizeof(nonce_bytes), &nonce_len, (const unsigned char*)nonce_base64.c_str(), nonce_base64.length());
  mbedtls_base64_decode(ciphertext_bytes, sizeof(ciphertext_bytes), &ciphertext_len, (const unsigned char*)ct_base64.c_str(), ct_base64.length());
  mbedtls_base64_decode(tag_bytes, sizeof(tag_bytes), &tag_len, (const unsigned char*)tag_base64.c_str(), tag_base64.length());

  unsigned char decryptedText[256];
  
  // 3. Eksekusi Dekripsi AES-GCM
  if (decrypt_aes_gcm(nonce_bytes, nonce_len, ciphertext_bytes, ciphertext_len, tag_bytes, tag_len, decryptedText)) {
    DynamicJsonDocument payloadDoc(512);
    deserializeJson(payloadDoc, decryptedText);
    
    int target_device_id = payloadDoc["id_device"];
    String action_value = payloadDoc["action"];
    
    int matchedIndex = -1;
    for (size_t i = 0; i < myDevices.size(); i++) {
      if (myDevices[i].id_device == target_device_id) {
        matchedIndex = i; break;
      }
    }
    
    if (matchedIndex != -1) {
      int pin = myDevices[matchedIndex].pin_gpio;
      int state = myDevices[matchedIndex].active_state;
      String statusTopic = "smartsystem/status/" + String(target_device_id);
      
      if (action_value == "CHANGE_STATUS_1") { 
        myDevices[matchedIndex].last_state = 1; 
        digitalWrite(pin, (state == 1) ? HIGH : LOW); 
        client.publish(statusTopic.c_str(), "1", true); 
        updateStateToDatabase(target_device_id, 1); 
        Serial.printf("[SUCCESS] DEVICE %d ON -> PIN %d\n", target_device_id, pin);
      } else if (action_value == "CHANGE_STATUS_0") { 
        myDevices[matchedIndex].last_state = 0; 
        digitalWrite(pin, (state == 1) ? LOW : HIGH); 
        client.publish(statusTopic.c_str(), "0", true); 
        updateStateToDatabase(target_device_id, 0); 
        Serial.printf("[SUCCESS] DEVICE %d OFF -> PIN %d\n", target_device_id, pin);
      }
    }
  }

  // --- AKHIR PENGUKURAN KINERJA ---
  uint32_t heap_after = ESP.getFreeHeap(); // Catat sisa RAM setelah proses
  unsigned long t_end = millis(); // Catat waktu selesai
  
  Serial.printf("[METRIK] Free Heap Akhir: %d Bytes\n", heap_after);
  Serial.printf("[HASIL] Konsumsi RAM AES: %d Bytes\n", (heap_before - heap_after));
  Serial.printf("[HASIL] Waktu Dekripsi & Eksekusi: %lu ms\n", (t_end - t_start));
  Serial.println("======================================");
}

void reconnect() {
  while (!client.connected()) {
    Serial.println("Menghubungkan ke MQTT Broker...");
    String clientId = "ESP32_Gateway_" + String(random(0xffff), HEX);
    if (client.connect(clientId.c_str(), "admin_pintu", "12345678")) {
      Serial.println("Terhubung!");
      client.subscribe(mqtt_topic_control);
      
      for (size_t i = 0; i < myDevices.size(); i++) {
        String statusTopic = "smartsystem/status/" + String(myDevices[i].id_device);
        client.publish(statusTopic.c_str(), String(myDevices[i].last_state).c_str(), true);
      }
    } else { delay(5000); }
  }
}

void setup() {
  Serial.begin(115200);
  setup_wifi();
  fetchDynamicConfiguration(); 
  setup_ntp(); 
  client.setServer(mqtt_server, mqtt_port);
  client.setBufferSize(1024);
  client.setCallback(callback);
}

void loop() {
  if (!client.connected()) reconnect();
  client.loop();
}