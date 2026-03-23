# Docker and Docker Compose Setup

# 1) Remove Old Docker
```
sudo dnf remove docker \
                docker-client \
                docker-client-latest \
                docker-common \
                docker-latest \
                docker-engine
```

# 2) Install DNF untils (my case)
`sudo dnf install -y dnf-plugins-core`

# 3) Add docker repo
`sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo`

# 4) Install docker 
`sudo dnf install -y docker-ce docker-ce-cli containerd.io`

# 5) Start Docker
`sudo systemctl start docker`\
`sudo systemctl enable docker`\
check if it works: `docker --version`\
Test: `sudo run docker run hello-world`

# 6) Install Docker Compose (plugin)
`sudo dnf install -y docker-compose-plugin`\
check: `docker compose version`
