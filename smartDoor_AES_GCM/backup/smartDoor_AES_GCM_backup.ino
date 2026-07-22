#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include "mbedtls/gcm.h"
#include "mbedtls/base64.h"

// ==========================================
// KONFIGURASI WIFI & MQTT
// ==========================================
const char* ssid        = "oplus_co_appvun";
const char* password    = "lahh1153";
const char* mqtt_server = "172.31.13.207"; // Samakan dengan backend PHP
const int   mqtt_port   = 1883;
const char* mqtt_topic  = "smartdoor/control/1"; // Angka 1 adalah ID pintu ESP32 ini

// ==========================================
// KONFIGURASI KRIPTOGRAFI (AES-256-GCM)
// ==========================================
// Kunci HARUS sama persis dengan yang ada di Backend PHP (32 Karakter = 256 bit)
const unsigned char* aes_key = (const unsigned char*)"4fE9xR2wL7pM1nQ8vB5zY3tS6hG0kC4j";

// ==========================================
// KONFIGURASI HARDWARE (RELAY)
// ==========================================
const int RELAY_PIN = 14; // Pin yang terhubung ke modul Relay (Selenoid Lock)

WiFiClient espClient;
PubSubClient client(espClient);

// Fungsi untuk menghubungkan WiFi
void setup_wifi() {
  delay(10);
  Serial.println();
  Serial.print("Menghubungkan ke ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi terhubung!");
}

// Fungsi Dekripsi AES-256-GCM
bool decrypt_aes_gcm(const unsigned char* iv, size_t iv_len, 
                     const unsigned char* ciphertext, size_t ciphertext_len, 
                     const unsigned char* tag, size_t tag_len, 
                     unsigned char* plaintext) {
                       
  mbedtls_gcm_context ctx;
  mbedtls_gcm_init(&ctx);
  mbedtls_gcm_setkey(&ctx, MBEDTLS_CIPHER_ID_AES, aes_key, 256);

  // Fungsi ini melakukan dekripsi sekaligus verifikasi Auth Tag
  int ret = mbedtls_gcm_auth_decrypt(&ctx, ciphertext_len, iv, iv_len, NULL, 0, tag, tag_len, ciphertext, plaintext);
  
  mbedtls_gcm_free(&ctx);

  if (ret == 0) {
    return true; // Autentikasi dan Dekripsi Berhasil
  } else {
    Serial.printf("Gagal Dekripsi! Error Code: %d\n", ret);
    return false; // Data korup, tag tidak cocok, atau kunci salah (Tampering detected!)
  }
}

// Fungsi yang dipanggil saat ada pesan MQTT masuk
void callback(char* topic, byte* payload, unsigned int length) {
  Serial.println("\n--- PESAN MQTT DITERIMA ---");
  
  // Ubah payload menjadi string
  String messageTemp;
  for (int i = 0; i < length; i++) {
    messageTemp += (char)payload[i];
  }

  // 1. Parsing JSON awal (yang berisi data terenkripsi)
  DynamicJsonDocument doc(1024);
  DeserializationError error = deserializeJson(doc, messageTemp);
  if (error) {
    Serial.println("Gagal parsing JSON MQTT");
    return;
  }

  const char* b64_iv = doc["iv"];
  const char* b64_ciphertext = doc["ciphertext"];
  const char* b64_tag = doc["tag"];

  if (!b64_iv || !b64_ciphertext || !b64_tag) {
    Serial.println("Format JSON tidak sesuai (Kehilangan iv/ciphertext/tag)");
    return;
  }

  // 2. Decode Base64 ke Byte Array
  unsigned char iv[16], ciphertext[256], tag[16], decryptedText[256];
  size_t iv_len, ciphertext_len, tag_len;

  mbedtls_base64_decode(iv, sizeof(iv), &iv_len, (const unsigned char*)b64_iv, strlen(b64_iv));
  mbedtls_base64_decode(ciphertext, sizeof(ciphertext), &ciphertext_len, (const unsigned char*)b64_ciphertext, strlen(b64_ciphertext));
  mbedtls_base64_decode(tag, sizeof(tag), &tag_len, (const unsigned char*)b64_tag, strlen(b64_tag));

  // 3. Lakukan Dekripsi GCM
  memset(decryptedText, 0, sizeof(decryptedText)); // Bersihkan memori buffer
  
  if (decrypt_aes_gcm(iv, iv_len, ciphertext, ciphertext_len, tag, tag_len, decryptedText)) {
    Serial.print("Hasil Dekripsi: ");
    Serial.println((char*)decryptedText);

    // 4. Parsing JSON dari hasil dekripsi
    DynamicJsonDocument decryptedDoc(512);
    DeserializationError decError = deserializeJson(decryptedDoc, decryptedText);
    
    if (!decError) {
      // Ambil nilai action sebagai INTEGER (Angka)
      int action = decryptedDoc["action"];
      
      // 5. Eksekusi Perintah Hardware
      if (action == 1) { // 1 = BUKA
        Serial.println("MEMBUKA PINTU...");
        digitalWrite(RELAY_PIN, LOW); 
        
        client.publish("smartdoor/status/1", "1", true); 
      } 
      else if (action == 0) { // 0 = KUNCI
        Serial.println("MENGUNCI PINTU...");
        digitalWrite(RELAY_PIN, HIGH); 
        
        client.publish("smartdoor/status/1", "0", true); 
      }
    } else {
      Serial.println("Gagal parsing JSON hasil dekripsi.");
    }
  } else {
    Serial.println("PERINGATAN: Potensi serangan (Tampering) atau Kunci Salah!");
  }
}

// Fungsi untuk menghubungkan ulang ke MQTT Broker jika terputus
void reconnect() {
  while (!client.connected()) {
    Serial.print("Menghubungkan ke MQTT Broker...");
    // Buat Client ID random
    String clientId = "ESP32DoorClient-";
    clientId += String(random(0xffff), HEX);
    
    if (client.connect(clientId.c_str(), "admin_pintu", "12345678")) {
      Serial.println("Terhubung!");
      // Subscribe ke topik spesifik pintu ini
      client.subscribe(mqtt_topic);
    } else {
      Serial.print("Gagal, rc=");
      Serial.print(client.state());
      Serial.println(" Coba lagi dalam 5 detik");
      delay(5000);
    }
  }
}

void setup() {
  Serial.begin(115200);
  
  // Setup Pin Hardware
  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, LOW); // Default terbuka  

  setup_wifi();
  
  // Setup MQTT
  client.setServer(mqtt_server, mqtt_port);
  client.setCallback(callback);

  client.publish("smartdoor/status/1", "1", true); 
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop(); // Biarkan MQTT tetap berjalan
}