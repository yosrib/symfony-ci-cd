
# Add SSH access for service account 

`gcloud projects add-iam-policy-binding PROJECT_ID \
    --member=user:EMAIL \
    --role=roles/iap.tunnelResourceAccessor`

`gcloud projects add-iam-policy-binding PROJECT_ID \
    --member=user:EMAIL \
    --role=roles/compute.instanceAdmin.v1`

Pour autoriser l'accès RDP à toutes les instances de VM de votre réseau, exécutez:

`gcloud compute firewall-rules create allow-rdp-ingress-from-iap \
  --direction=INGRESS \
  --action=allow \
  --rules=tcp:3389 \
  --source-ranges=35.235.240.0/20`

Pour l'accès SSH, exécutez la commande suivante:

`gcloud compute firewall-rules create allow-ssh-ingress-from-iap \
  --direction=INGRESS \
  --action=allow \
  --rules=tcp:22 \
  --source-ranges=35.235.240.0/20`