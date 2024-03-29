# Workflow steps
```yaml
jobs:
  build:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - id: 'auth'
        name: Google cloud setup
        uses: 'google-github-actions/auth@v0'
        with:
          token_format: 'access_token'
          credentials_json: '${{ secrets.GCP_SERVICE_ACCOUNT_KEY }}'
          project_id: ${{ secrets.GCP_PROJECT_ID }}
          # service_account_email: ${{ secrets.GCP_SERVICE_ACCOUNT }}
          # service_account_key: ${{ secrets.GCP_SERVICE_ACCOUNT_KEY }}

      #- name: Use gcloud CLI
      #  run: gcloud info

      - name: Docker login
        uses: 'docker/login-action@v1'
        with:
          registry: 'gcr.io' # or REGION-docker.pkg.dev
          username: 'oauth2accesstoken'
          password: '${{ steps.auth.outputs.access_token }}'


      #- name: Configure docker authentication
      #  run: gcloud auth configure-docker gcr.io -q

      #- name: Build and push
      #  uses: docker/build-push-action@v3
      #  with:
      #    push: true
      #    tags: ${{ secrets.GCR_IMAGE }}

      #- name: Build the Docker image
      #  run: docker build . -f Dockerfile -t ${{ secrets.GCR_IMAGE }}

      #- name: Push the Docker image
      #  run: docker push ${{ secrets.GCR_IMAGE }}

      - name: Build docker image
        run:  docker build . -t gcr.io/${{ secrets.GCP_PROJECT_ID }}/symfony-ci-cd

      - name: Push to Google Container Registry
        run:  docker push gcr.io/${{ secrets.GCP_PROJECT_ID }}/symfony-ci-cd
```
