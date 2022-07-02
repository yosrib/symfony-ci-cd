# Symfony CI CD

## Tags
`jenkins` `kubernetes` `ansible` `snarqube` `google container registry`

# Push image to google container registry
## Auth
https://cloud.google.com/container-registry/docs/advanced-authentication

$ gcloud auth login
## Change project setting
$ gcloud config set project PROJECT_ID
## Install updates
$ gcloud components update
## Configure docker authentication with Container Registry
$ gcloud auth configure-docker gcr.io
https://cloud.google.com/sdk/gcloud/reference/auth/configure-docker

# References
### SSH google compute engine
https://github.com/google-github-actions/ssh-compute

### Jenkins installs as container
https://www.jenkins.io/doc/book/installing/docker/