# Deplowy.yaml Guide
**Goal:** A "Cheat Sheat" for writing the CICD Config Files

The deployment configuration file lives at `.github/workflows/deploy.yml`.\

[!IMPORTANT]
**YAML Rule of Thumb:** Always use 2 spaces for indentation. Never use the Tab key, as it will break the workflow.


**Example workflow file below**
```
name: namee_you_see_on_github

on: #what trigger
  push: #the thing you want to trigger the workflow
    branches: #in our case we want it on x branches
      - main #this is the branch we want it on, this means TRIGGER ONLY WHEN CODE IS PUSHED TO THE MAIN BRANCH

jobs: #you declare the jobs you want to do
  job_1: #in our case just one job, you can have multiple jobs
    runs-on: self-hosted #we run this job on the self-hosted runner on github, THIS JOB EXECUTES ON MAIN LINUX SERVER
    env: #this is just variables to make our writing easier
      WEB_ROOT: /var/www/html
      APP01: 192.123.456.78
      APP02: 192.123.456.79
    steps:
      # The below are mandatory and what it does is that it pulls code from GitHub to the runner
      - name: Checkout code
        uses: actions/checkout@v4

      # Example Ansible workflow, here we installing rsync on all servers, via an already written playbook
      - name: Install Rsync on all servers (name of one of our sub job in the main job)
        run: ansible-playbook -i $GITHUB_WORKSPACE/ansible/configs/inventory.ini $GITHUB_WORKSPACE/ansible/playbooks/install_rsync.yml --extra-vars "ansible_become_password=${{ secrets.BECOME_PASSWORD }}" #run means execut ethis command on our server
        env:
          BECOME_PASSWORD: $ {{ secrets.BECOME_PASSWORD }} # Pulling the sensitive password from GitHub Secrets vault

      # Another sub job, all its doing is just copying web files over to nginx web files location
      - name: Deploy web files to app01
        run: rsync -avz $GITHUB_WORKSPACE/app/* ${{ env.WEB_ROOT }}/
      - name: Deploy web files to app02
        run: rsync -avz $GITHUB_WORKSPACE/app/* root@${{ env.APP02 }}:${{ env.WEB_ROOT }}/

```

### Deep Dive: Key Concepts
1. What is `actions/checkout@v4` ?
When your runner starts a job, it begins in an empty temporary directory. It does not automatically have your code.
* The "Checkout" action is a pre-made script by GitHub that logs into your repository, pulls the latest code, and places it into a folder called `$GITHUB_WORKSPACE`.
* Without this step, your ansible-playbook or rsync commands or any other command would fail because there would be no files to move or interact with!

2. Understanding GitHub secrets.
You should never hardcode passwords (like your Linux sudo password) directly into your YAML file. If you do, anyone with access to the repo can see it.
* **GitHub Secrets:** An encrypted vault in your repository settings (**Settings > Secrets and variables > Actions**).
* **Usage:** You save your password there as `BECOME_PASSWORD`
* **Security:** GitHub automatically masks these in the logs. If your script tries to print the password, GitHub will replace it with ***

3. What is `$GITHUB_WORKSPACE`?
This is a built-in "Environment Variable." It automatically points to the absolute path where your code was downloaded on the runner. Using this ensures your paths are always correct, regardless of where the runner folder is located on your Linux server.

### Anatomy of a Workflow file
| Key | Description |
| :--- | :--- |
| `name` | The dispaly name of the action in the github UI. |
| `on` | The event that triggers the workflow (e.g., `push`, `pull_request` or `schedule`). |
| `runs-on` | Defines the environment. `self-hosted` targets your local server |
| `env` | Defines variables you can reuse throughout the script to save time. |
| `steps` | The list of actions to take. A sequence of tasks. Each task is either a pre-made `action` or a shell `run` command. |

### Common Commands
* `uses: actions/checkout@v4`: This is essential. It "clones" your repo onto the runner's folder so it can work with the files.
* `run`: Executes any bash command (e.g., `cp`, `systemctl restart`, `npm install`).

### Troubleshooting
* Ensure the directory is `.github/workflows/` (exactly as written).
* YAML is indentation-sensitive. Use 2 spaces, never tabs.