# Setting Up Docker & Docker Compose on AWS EC2 (Amazon Linux 2023)

This guide covers installing Docker and Docker Compose on a fresh Amazon Linux 2023 EC2 instance.
Amazon Linux 2023 uses its own package repository — the standard Docker CE repo used for CentOS/RHEL does not work here.

---

## 1. Install Docker

```bash
sudo dnf install -y docker
```

Start the Docker service and enable it to start automatically on reboot:

```bash
sudo systemctl start docker
sudo systemctl enable docker
```

---

## 2. Add Your User to the Docker Group

When Docker installs, it creates a `docker` system group. Only root and members of this group can run Docker commands without `sudo`. Add your user:

```bash
sudo usermod -aG docker ec2-user
```

Apply the group change without logging out:

```bash
newgrp docker
```

---

## 3. Install Docker Compose

Amazon Linux 2023's Docker package does not bundle Compose by default. Install it manually as a Docker CLI plugin:

```bash
sudo mkdir -p /usr/local/lib/docker/cli-plugins

sudo curl -SL https://github.com/docker/compose/releases/latest/download/docker-compose-linux-x86_64 \
  -o /usr/local/lib/docker/cli-plugins/docker-compose

sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
```

---

## 4. Install Docker Buildx (if needed)

The version of Buildx bundled with Amazon Linux's Docker package may be too old to support `docker compose build`. If you hit the error `compose build requires buildx 0.17.0 or later`, install a newer version manually:

```bash
sudo curl -SL "https://github.com/docker/buildx/releases/download/v0.21.2/buildx-v0.21.2.linux-amd64" \
  -o /usr/local/lib/docker/cli-plugins/docker-buildx

sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-buildx
```

---

## 5. Verify

```bash
docker --version
docker compose version
docker buildx version
```

---

## 6. Open Port 80 in AWS Security Groups

By default, EC2 instances only allow inbound SSH traffic on port 22. To make a web app accessible:

1. AWS Console → EC2 → Your instance → **Security** tab
2. Click the Security Group → **Edit inbound rules**
3. Add rule: Type = **HTTP**, Port = **80**, Source = **0.0.0.0/0**
4. **Save rules**

`0.0.0.0/0` allows traffic from any IP address.

---

## 7. Deploy

```bash
git clone https://github.com/devToluwa/Enterprise-Linux-Labs.git
cd Enterprise-Linux-Labs/docker
vi .env        # add DB_HOST, DB_USER, DB_PASS, DB_NAME
docker compose up -d --build
```

Find your instance's public IP:

```bash
curl ifconfig.me
```

Open `http://<your-public-ip>` in a browser.