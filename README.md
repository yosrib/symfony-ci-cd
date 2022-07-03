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

# Github action
# References
### SSH google compute engine
https://github.com/google-github-actions/ssh-compute
### Build and push to GCR
https://github.com/marketplace/actions/push-to-gcr-github-action
https://medium.com/mistergreen-engineering/uploading-a-docker-image-to-gcr-using-github-actions-92e1cdf14811

### Jenkins installs as container
https://www.jenkins.io/doc/book/installing/docker/