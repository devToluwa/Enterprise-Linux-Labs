#!/bin/bash

# Install Docker
sudo dnf install -y docker
sudo systemctl start docker
sudo systemctl enable docker


# Install Docker compose
sudo mkdir -p /usr/local/lib/docker/cli-plugins
sudo curl -SL https://github.com/docker/compose/releases/latest/download/docker-compose-linux-x86_64 \
  -o /usr/local/lib/docker/cli-plugins/docker-compose


# Install Docker Buildx(if needed, but it asked me to on aws so i add it here too)
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
sudo curl -SL "https://github.com/docker/buildx/releases/download/v0.21.2/buildx-v0.21.2.linux-amd64" \
  -o /usr/local/lib/docker/cli-plugins/docker-buildx
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-buildx


# Verify installation
docker --version
docker compose version
docker buildx version


# Add your user to the Docker group
# This is here cause it creates a new shell, and logs you in as docker
sudo usermod -aG docker ec2-user
newgrp docker

# newgrp opens a new shell as the docker group
# exit closes it and returns you to your normal ec2-user session
# run 'docker ps' to confirm you no longer need sudo
exit