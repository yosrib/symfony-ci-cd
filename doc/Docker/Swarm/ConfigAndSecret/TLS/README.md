# TLS

## Certification Authority (CA)
### Private key
$ openssl genrsa -out ca-key.pem 2048

### Auto signed certificate
$ openssl req -new -x509 -days 365 -key ca-key.pem -sha256 -subj "/C=FR/L=Nice/O=MyOrg/CN=ca" -out ca.pem

## Private / Public key for domain
### Private key
$ openssl genrsa -out server-key.pem 2048

### Certificate Signin Request
$ openssl req -new -subj "/C=FR/L=Nice/O=MyOrg/CN=city.com" -key server-key.pem -out server.csr

### Sign certificate
$ openssl x509 -req -days 365 -in server.csr -CA ca.pem -CAkey ca-key.pem -CAcreateserial -out server-cert.pem

