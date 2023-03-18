# IAM Identity Access Management
Centraliser la gestion des utilisateurs
Securites les utilisateur grace au MFA
Partager
Gerer les permissions
Federation d'autres annuaires (branchement avec AD ou google...)
=> qui fait quoi
=> autoriser ou interdire des operations
(audit) CloudTrail => d'obtenir les journaux de tous les acces utilises
## Access interfaces
Dashboard (administration console)
Command line

## Authentications
Dashboard (administration console)
- username
- password
  SSH
- access key
- secret key id
  MFA (Multi-Factor Authentication)
- Material token (exp RSA secure ID)
- Google Authenticator

## Roles
- Strategie d'aprobation (Trust Policy)
- Strategir d'autorisation (Access Policy)

## IAM Strategy
=> collection of statements (json)
```json
{
  "Statement": [{
    "Effect": "EFFECT",
    "Principal": "PRINCIPAL",
    "Action": ["ACTION", "ACTION"],
    "Resource": "ARN",
    "Condition": {
      "CONDITION": {
        "KEY": "VALUE"
      }
    }
  }]
}
```

### Effect

### Resource
ARN (Amazon Resource Name) => the identifier of a service
exp: arn:aws:iam:SERVICE_IDENTIFIER

### Action
The operation into a service
exp:
- Create EC2 instance => ec2:StartInstances
- Get S3 object => s3:GetObject

### NotAction
exp: allow all resources except iam actions
```json
{
  "Version": "2023-02-06",
  "Statement": [{
    "Effect": "Allow",
    "NotAction": "iam:*",
    "Resource": "*"
  }]
}
```
Not action is not a denied

### Condition
Apply only for some condition, for example only for IP or only between 2 dates