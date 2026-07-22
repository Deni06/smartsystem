# Setup untuk SSL Certificates

## Opsi 1: Self-Signed Certificate (Development)

Untuk development/testing, generate self-signed certificate:

```bash
mkdir -p nginx/ssl

# Generate private key
openssl genrsa -out nginx/ssl/key.pem 2048

# Generate certificate (valid 365 hari)
openssl req -new -x509 -key nginx/ssl/key.pem -out nginx/ssl/cert.pem -days 365 \
  -subj "/C=ID/ST=State/L=City/O=Organization/CN=smartdoor.example.com"
```

## Opsi 2: Let's Encrypt Certificate (Production)

Untuk production, gunakan Let's Encrypt dengan Certbot:

```bash
# Install Certbot
sudo apt-get install certbot python3-certbot-nginx

# Generate certificate
sudo certbot certonly --nginx -d smartdoor.example.com -d phpmyadmin.example.com

# Copy certificates ke nginx/ssl
sudo cp /etc/letsencrypt/live/smartdoor.example.com/fullchain.pem nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/smartdoor.example.com/privkey.pem nginx/ssl/key.pem

# Set permissions
sudo chown user:user nginx/ssl/*
chmod 600 nginx/ssl/*
```

## Opsi 3: Commercial SSL Certificate

Hubungi provider SSL pilihan Anda dan follow instructions mereka.

## Konfigurasi di nginx.conf

Update domain di `nginx/nginx.conf`:

```nginx
server_name smartdoor.example.com;  # Ganti dengan domain Anda
ssl_certificate /etc/nginx/ssl/cert.pem;
ssl_certificate_key /etc/nginx/ssl/key.pem;
```

## Certbot Auto Renewal

Untuk auto-renew Let's Encrypt certificates:

```bash
# Test renewal
sudo certbot renew --dry-run

# Setup cron job (usually already done)
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

## Struktur Folder

```
nginx/
├── nginx.conf
└── ssl/
    ├── cert.pem
    └── key.pem
```
