# Docker & AWS Deployment — Enterprise Linux Labs

## Overview

This document covers two things: containerizing the Enterprise Linux Labs web application using Docker and Docker Compose, and deploying that containerized stack to a live AWS EC2 instance accessible from anywhere on the internet.

The application itself is a user management web app — anyone can open it in a browser, add a name and role via a form, and the entry gets written to a MariaDB database. The table updates on reload. The frontend and backend are written in PHP, with the frontend styled using Tailwind CSS.

---

## The Application Stack

Before containerizing, the app ran across three CentOS 9 VMs:

- `app01` — Nginx reverse proxy / load balancer + php-fpm
- `app02` — Nginx web server + php-fpm
- `db01` — MariaDB database server

The goal of the Docker version was to replicate this same architecture in containers so the entire stack could be spun up on any machine with a single command, no manual VM provisioning required.

---

## Docker Architecture

The containerized stack is defined in a single `docker-compose.yml` file with four services:

### 1. `nginx` — Load Balancer
Uses the official `nginx:latest` image. This container receives all incoming HTTP traffic on port 80 and load balances requests between the two app servers using a custom Nginx config mounted via a volume. It does not run PHP — its only job is routing.

### 2. `app01` and `app02` — PHP App Servers
These two containers simulate the two app servers from the VM setup, allowing Nginx to round-robin traffic between them. Each request is served by either `app01` or `app02` — the app displays the hostname of whichever container handled the request, which visually confirms load balancing is working on every reload.

Both containers are built from a custom Dockerfile rather than a plain image. The reason: the official `php:8-fpm` image does not include the `mysqli` extension by default, which the app needs to connect to MariaDB. The Dockerfile adds it:

```dockerfile
FROM php:8-fpm
RUN docker-php-ext-install mysqli
```

`php:8-fpm` means the container runs PHP-FPM — a PHP process manager that handles PHP execution. Nginx forwards `.php` requests to these containers via FastCGI (a protocol for communication between a web server and a backend process), rather than handling PHP itself.

Both app containers have the PHP application files mounted as a volume from the host, so no files are baked into the image. This means code changes don't require rebuilding the image.

Database credentials (host, user, password, database name) are passed into both app containers via an `env_file` pointing to a `.env` file. The PHP config reads these using `getenv()`. The `.env` file is not committed to the repo.

### 3. `db` — MariaDB Database
Uses the official `mariadb:latest` image. Database name, user, and password are passed as environment variables directly in the Compose file. The MariaDB image automatically creates the database and user on first start using these variables.

The application requires a `users` table to exist before it can run. The MariaDB image automatically executes any `.sql` files mounted into `/docker-entrypoint-initdb.d/` on first start. An `init.sql` file is mounted there to create the table:

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL
);
```

### Networking
All four containers communicate over a Docker network created automatically by Compose. Containers reference each other by service name — for example, Nginx routes PHP requests to `app01:9000` and `app02:9000`, and the PHP app connects to the database at `db:3306`. No IP addresses are hardcoded.

Only the Nginx container exposes a port to the outside world (`80:80`). The app and database containers are internal only.

---

## Key Concepts

**Why a custom Dockerfile for the app containers?**
When an official image doesn't have everything you need, you write a Dockerfile to extend it. `build: ./app` in the Compose file tells Docker to build a custom image from the Dockerfile in the `./app` folder instead of pulling one from Docker Hub.

**Why does Nginx need the HTML files if php-fpm handles PHP?**
Nginx needs the files to resolve paths and serve any static assets. php-fpm needs the files to actually execute the PHP. Both containers mount the same `./html` volume for this reason.

**Why no ports on app01 and app02?**
They're internal services. Only Nginx needs to be reachable from outside — everything behind it communicates over Docker's internal network without exposing ports to the host.

---

## Repo Structure

```
docker/
├── docker-compose.yml
├── .env                          # not committed — credentials
├── app/
│   └── Dockerfile                # php:8-fpm + mysqli
├── nginx/
│   └── default.conf              # load balancer config
├── html/
│   ├── index.php
│   ├── add_user.php
│   ├── delete_user.php
│   └── db_config.php
└── db/
    └── init.sql                  # creates users table on first start
```

---

## Running the Stack

```bash
git clone https://github.com/devToluwa/Enterprise-Linux-Labs.git
cd Enterprise-Linux-Labs/docker
# create your .env file with DB_HOST, DB_USER, DB_PASS, DB_NAME
docker compose up -d --build
```

To stop:
```bash
docker compose down
```

To stop and wipe the database (forces re-run of init.sql on next start):
```bash
docker compose down -v
```

---

## AWS EC2 Deployment

The same `docker-compose.yml` was deployed to an AWS EC2 instance (Amazon Linux 2023) to make the app publicly accessible.

### Installing Docker on Amazon Linux 2023

Amazon Linux 2023 uses its own package repository. The standard Docker CE repo used for CentOS/RHEL does not work here. The correct installation:

```bash
sudo dnf install -y docker
sudo systemctl start docker
sudo systemctl enable docker
```

`systemctl enable` makes Docker start automatically on reboot. Without this, the containers go down every time the instance restarts.

### Adding ec2-user to the Docker Group

When Docker installs on Linux, it creates a `docker` system group. Only root and members of this group can run Docker commands. By default, `ec2-user` is not in this group, which is why every Docker command requires `sudo`. Adding the user to the group fixes this:

```bash
sudo usermod -aG docker ec2-user
newgrp docker
```

You need to log out and back in (or run `newgrp docker`) for the group change to take effect.

### Installing Docker Compose

Amazon Linux 2023's version of Docker does not bundle Compose as a CLI plugin by default. It needs to be installed manually into the Docker CLI plugins directory:

```bash
sudo mkdir -p /usr/local/lib/docker/cli-plugins
sudo curl -SL https://github.com/docker/compose/releases/latest/download/docker-compose-linux-x86_64 \
  -o /usr/local/lib/docker/cli-plugins/docker-compose
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
```

This places the Compose binary where Docker expects to find CLI plugins, making `docker compose` work as a subcommand.

A similar manual install was needed for Docker Buildx (the build engine), as the version bundled with Amazon Linux's Docker package was too old to support the Compose build process:

```bash
sudo curl -SL "https://github.com/docker/buildx/releases/download/v0.21.2/buildx-v0.21.2.linux-amd64" \
  -o /usr/local/lib/docker/cli-plugins/docker-buildx
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-buildx
```

### Opening Port 80 in AWS Security Groups

AWS EC2 instances are protected by Security Groups — effectively a firewall that controls what traffic can reach the instance. By default, only port 22 (SSH) is open. To make the web app accessible:

1. AWS Console → EC2 → Your instance → Security tab
2. Click the Security Group → Edit inbound rules
3. Add rule: Type = HTTP, Port = 80, Source = `0.0.0.0/0`
4. Save rules

`0.0.0.0/0` means the rule applies to all source IPs — anyone on the internet can reach port 80. This is intentional for a public-facing web app.

### Deploying

```bash
git clone https://github.com/devToluwa/Enterprise-Linux-Labs.git
cd Enterprise-Linux-Labs/docker
vi .env  # add your credentials
docker compose up -d --build
```

The app is then accessible at the instance's public IP on port 80. The EC2 public IP can be found in the AWS console or by running:

```bash
curl ifconfig.me
```

---

## Troubleshooting Notes

**VMware NAT internet access lost** — If Docker image pulls fail on a local VM with `i/o timeout` on DNS, the VMware NAT Service on the Windows host may have stopped. Fix: restart `VMware NAT Service` in Windows Services or via:
```cmd
net stop "VMware NAT Service"
net start "VMware NAT Service"
```

**403 Forbidden from Nginx** — Usually caused by a space in the volume mount path in `docker-compose.yml` (e.g., `./html :/var/www/html`). Docker creates a new directory with the space in the name and mounts that empty directory instead of the actual html folder. Fix: remove the space, run `docker compose down`, delete the incorrectly created directory, and bring the stack back up.

**Table doesn't exist error** — The `init.sql` file either has invalid SQL or the MariaDB container already initialized without it. Fix: correct the SQL and run `docker compose down -v` to wipe the volume and force re-initialization.

**Compose build requires buildx 0.17.0 or later** — The bundled buildx version is too old. Manually install a newer buildx binary as shown in the installation steps above.