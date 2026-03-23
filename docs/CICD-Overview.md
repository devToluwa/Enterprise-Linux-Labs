# CICD Overview

## What is CD/CD
CI/CD is a way to introduce automation in an application environment, especially prod
for example you have netflix, i type some code on my laptop as a remote guy to change the color of a button, i push that code to github, and after all the approvals, the code gets pushed to the server automatically, thus it getting pushed to live instantly, now for complex stuff they could be packaged in like an update or something, but this is just a general overview of how it is

**Continuous Integration(CI):** Automates the building, packaging, and testing of applications every time a team member pushes changes to version control
**Continuous Deployment(CD):** Automatically pickes up the ***"passed"*** code from the CI stage and deploys it to the production or staging environment.
