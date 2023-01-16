# Harbor

## Installation
curl -L https://github.com/goharbor/harbor/releases/download/v2.5.0/harbor-online-installer-v2.5.0.tgz -o harbor.tgz
tar xvf harbor.tgz

cd harbor
cp harbor.yml.tmpl harbor.yml

### Options
--with-notary: Notary permet la création et la vérification de signatures sur les images (nécessite la configuration TLS)
--with-trivy: Trivy est également un scanner de vulnérabilités
--with-chartmuseum: ChartMuseum permet de distribuer des application packagées dans des charts Helm (utilisé dans le monde Kubernetes)

$ sudo ./install.sh --with-trivy --with-chartmuseum

default identifiers admin/Harbor12345
