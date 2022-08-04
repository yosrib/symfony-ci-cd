# CertificateSigningRequest

## User actions
openssl genrsa -out david.key 4096
openssl req -new -subj "/O=dev/CN=david" -key david.key -out david.csr

## Admin actions
export BASE64_CSR=$(cat ./david.csr | base64 | tr -d '\n')

### Create the resource mycsr
cat csr.yml | envsubst | kubectl apply -f -

### Approve the resource mycsr
kubectl certificate approve mycsr

### Retrieve .crt
kubectl get csr mycsr -o jsonpath='{.status.certificate}' | base64 --decode > david.crt

### Check .crt
openssl x509 -in ./david.crt -noout -text

# Config

## Admin actions
export USER="david"

export CLUSTER_NAME=$(kubectl config view --minify -o jsonpath={.current-context})

export CLIENT_CERTIFICATE_DATA=$(kubectl get csr mycsr -o jsonpath='{.status.certificate}')

export CLUSTER_CA=$(kubectl config view --minify --raw -o json | jq -r '.clusters[0].cluster["certificate-authority-data"]')

export CLUSTER_ENDPOINT=$(kubectl config view --minify --raw -o json | jq '.clusters[0].cluster["server"]')

cat kubeconfig.tpl | envsubst > kubeconfig

## User actions
export KUBECONFIG=$PWD/kubeconfig

kubectl config set-credentials david \
--client-key=$PWD/david.key \
--embed-certs=true

