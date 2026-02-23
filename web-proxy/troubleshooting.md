# Web Proxy Issues & Resolutions

## 1. SELinux Blocking Proxy
- **Issue**: Nginx returns 502/503 even when backend is up.
- **Fix**: `sudo setsebool -P httpd_can_network_connect 1`

## 2. SSL/HTTPS Setup
- **Command**: `openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/nginx.key -out /etc/nginx/ssl/nginx.crt`
- **Firewall**: Must open port 443 via `firewall-cmd --add-service=https`.

## 3. Load Balancing
- Used an `upstream` pool named `my_app_pool` to point to app02.
