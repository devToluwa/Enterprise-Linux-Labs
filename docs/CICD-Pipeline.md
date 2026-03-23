# The Pipeline Logic

This project utilizes **GitHub Actions** with a **Self-Hosted Runner** to manage deployments on local infrastructure.

### The Workflow Flowchart
1.  **Code Push:** A developer pushes code to the `main` branch.
2.  **Trigger:** GitHub detects the push and looks for `.github/workflows/deploy.yml`.
3.  **Job Assignment:** GitHub sends the "Instructions" to our local Runner.
4.  **Execution:** The Runner (on our Linux server) executes the shell commands defined in the YAML file.
5.  **Status:** GitHub reports whether the deployment succeeded or failed.

### Infrastructure Components
* **VCS:** GitHub Repository.
* **Runner Platform:** RHEL/CentOS Linux VM.
* **Automation Engine:** GitHub Actions Runner Service.