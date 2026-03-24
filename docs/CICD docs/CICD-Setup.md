# Github Actions Runner Setup
### Prerequisites
* A Linux server with internet access running the app
* A user with `sudo` privileges

### Step 1. Download and extract
```
Create directory
mkdir actions-runner && cd actions-runner

# Download the runner package
curl -o actions-runner-linux-x64-2.322.0.tar.gz -L [https://github.com/actions/runner/releases/download/v2.322.0/actions-runner-linux-x64-2.322.0.tar.gz](https://github.com/actions/runner/releases/download/v2.322.0/actions-runner-linux-x64-2.322.0.tar.gz)

# Extract
tar -xzf ./actions-runner-linux-x64-2.322.0.tar.gz
```

### Step 2. Configuration
1. In Github, go to: **Your Repo > Settings > Actions > New self-hosted Runner**
2. Copy the `./config.sh` command provided by Github and run it on your server.
3. When prompted for "Lables", you can leave it default or add production..

### Step 3. Persistence (Optional, but used in Real-World Environments)
To ensure the runner is starts automatically after a server reboot, install it as a **systemd service**
```
sudo ./svc.sh install
sudo ./svc.sh start
```
