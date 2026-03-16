# Enterprise Linux Labs

A multi-VM enterprise Linux environment built from scratch to simulate real-world infrastructure — covering networking, web serving, load balancing, database administration, and automation.

---

## 🏗️ Architecture

Three CentOS 9 VMs on a private network:

| Host   | IP       | Role                          |
|--------|----------|-------------------------------|
| app01  | .10      | Load Balancer (Nginx)         |
| app02  | .11      | Web Server (Nginx)            |
| db01   | .12      | Database Server (MariaDB)     |

Traffic hits `app01` → load balanced to `app02` → app talks to `db01` on a restricted port.

---

## ✅ What's Been Built

**Phase 1 – Foundation**
- Provisioned 3x CentOS 9 VMs with static IPs, hostnames, and SSH key auth
- Configured `/etc/hosts` for name-based resolution across all nodes
- Set up GitHub repo with SSH key integration

**Phase 2 – Web & Proxy Layer**
- Deployed Nginx on `app01` as a reverse proxy / load balancer
- Deployed Nginx on `app02` serving a web application
- Generated and installed a self-signed SSL certificate for HTTPS

**Phase 3 – Database Layer**
- Installed and hardened MariaDB on `db01` (`mysql_secure_installation`)
- Created application user and production database
- Locked down port 3306 via firewalld — only app server IPs allowed
- Verified remote DB connection from `app01` via CLI

**Phase 4 – Automation**
- Installed Ansible on `app01` as the control node
- Wrote `inventory.ini` targeting all nodes
- Created Ansible playbook to automate Nginx deployment
- Bash script to back up the MariaDB database and push to Git

**Phase 5 – Containers (In Progress)**
- Docker and Docker Compose setup underway
- Containerizing services for portable, consistent deployment

---

## 📁 Repo Structure
```
Enterprise-Linux-Labs/
├── ansible/        # Playbooks and inventory
├── database/       # DB setup scripts and config
├── docker/         # Dockerfiles and Compose files
├── docs/           # Architecture notes and SOPs
├── networking/     # Firewall rules, hosts config
├── scripts/        # Bash utilities and backup scripts
├── terraform/      # Cloud infrastructure (planned)
└── web-proxy/      # Nginx load balancer and SSL config
```

---

## 🔧 Stack

`CentOS 9` · `Nginx` · `MariaDB` · `Ansible` · `Docker` · `Bash` · `Git`

---

## 🎯 Goals

- Simulate a production-grade Linux infrastructure in a homelab
- Practice infrastructure automation end-to-end (provisioning → deployment → backup)
- Build toward full CI/CD pipeline and cloud integration (Terraform + AWS)

---

*Part of an ongoing DevOps learning path. See also: [HomeLabs001](https://github.com/devToluwa/HomeLabs001)*
