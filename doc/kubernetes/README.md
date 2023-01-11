# Kubernetes


Cluster => groupe de nodes

nodes (mater => gestion du cluster ou worker => tourner des applications client)

Service

Pod (groupe de contenaire dans le meme reseau et partageant le meme stockage)


Deployment => mettre a jour l'image

Service (expose des pods) regroupement de pod similaire 
label & annotation (key:value) present dans les metadata (utilise par les applications externe)


Utilisation
recuperer le fichier de conf (config)



# Kubectl
export KUBECONFIG=CONGIG_FILE_PATH

## Show kubernetes api-resources
$ kubectl api-resources

## Show kubernetes api-versions
$ kubectl api-versions

# Explain
$ kubectl explain <yaml.pattern.to.field>
$ kubectl explain pod.spec.containers.command

## Installation
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Installation/kubectl.md
https://github.com/ahmetb/kubectx

## Commands
### Create a POD
#### With YAML file
kubectl create -f pod.yaml
kubectl apply -f pod.yaml

#### With run command
kubectl run POD_NAME --image=DOCKER_IMAGE --restart=Never

### Describe a POD
kubectl describe pod POD_NAME

### Get Node YAML based on pod creation simulation
kubectl run --image=mongo:4.0 --dry-run=client -o yaml
kubectl run --image=mongo:4.0 --dry-run=server -o yaml

### Get PODS
kubectl get pods (default namespace)
kubectl get pods -A (lister les pods dans tous les namespaces)
kubectl get pods --all-namespaces (lister les pods dans tous les namespaces)
kubectl get pods --namespace=NAMESPACE_NAME (lister les pods dans un namespace)
kubectl get pods -w (watch live update)


### Inspect POD logs
kubectl logs POD_NAME -c CONTAINER_NAME

### Command execution
kubectl exec POD_NAME -c CONTAINER_NAME -- COMMAND

### Delete POD
kubectl delete pod POD_NAME

### Port forward
kubectl port-forward POD_NAME LOCAL_PORT:POD_PORT
kubectl port-forward www 8080:80

Note: si vous utilisez une machine virtuelle intermédiaire pour accéder à votre cluster, vous pourrez utiliser l'option --address 0.0.0.0 pour la commande port-forward afin de permettre l'accès depuis toutes les interfaces réseau de votre machine.
kubectl port-forward --address 0.0.0.0 POD_NAME LOCAL_PORT:POD_PORT

## Nodes
### List nodes
kubectl get nodes
kubectl get nodes -o wide (get nodes with internal/external IP)

### Add label to node
kubectl label nodes NODE_NAME LABEL_NAME=LABEL_VALUE

### Get Node YAML
kubectl get node/NODE_NAME -o yaml

## Services
### ClusterIp
Expose pods in internal
kubectl expose pod POD_NAME --type=ClusterIP --port=PORT --target-port=TARGET_PORT

### NodePort
Expose pods in external (port number between 30 000 and 32 767)
kubectl apply -f service-np.yaml

- Show NodePort yaml config without create it
kubectl expose pod POD-NAME --type=NodePort --port=8080 --target-port=80 --dry-run=client -o yaml (expose pod)
kubectl create service nodeport POD-NAME --tcp 8080:80 --dry-run=client -o yaml (create service)
kubectl run POD-NAME --image IMAGE-NAME --port=27017 --expose --dry-run=client -o yaml (create and expose pod)

### Show service
kubectl get services SERVICE_NAME
kubectl get svc/SERVICE_NAME -o yaml

### Describe service
kubectl describe services SERVICE_NAME

## Example
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Pod/pod_whoami.md

## Deployment
### List deployments
kubectl get deploy
kubectl get rs (replicaset list)

### Create deployment
kubectl create deploy DEPLOY_NAME --image=DOCKER_IMAGE
kubectl create deploy DEPLOY_NAME --image=DOCKER_IMAGE --replicas=3

### Update deployment
kubectl set image deploy/DEPLOY_NAME IMAGE_NAME=DOCKER_IMAGE:TAG --record

### Rollback deployment
kubectl rollout undo deploy/DEPLOY_NAME (rollback to previous version)
kubectl rollout undo deploy/DEPLOY_NAME --to-revision=REVISION_NUMBER

### Force deployment update
kubectl rollout restart deploy/DEPLOY_NAME

### Show deploy update history
kubectl rollout history deploy/DEPLOY_NAME (show revisions)

### Get container image from deploy
kubectl get deploy/DEPLOY_NAME -o jsonpath='{.spec.template.spec.containers[0].image}'

## Scaling
kubectl scale -h
kubectl scale --replicas=3 deploy/DEPLOY_NAME

## HorizontalPodAutoscaler
### Requirements
NOTE: install metrics-server for automatic scalability https://github.com/kubernetes-sigs/metrics-server
- check if installed
kubectl get po -n kube-system -l k8s-app=metrics-server
- install it 
kubectl apply -f https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml
- get nodes cpu usage
kubectl top nodes
- Send multiple request to pod to test autoscaling (with Apache Bench https://httpd.apache.org/docs/current/programs/ab.html)
kubectl run ab -ti --rm --restart='Never' --image=lucj/ab -- -n 200000 -c 100 http://w3/

### Check autoscaling versions
kubectl api-versions | grep autoscaling

### Create autoscaling
kubectl autoscale deploy DEPLOY_NAME --min=2 --max=10 --cpi-percent=50

### Show autoscaler
kubectl get AUTOSCALING_NAME -w (watch)

## Namespaces

### Get namespaces
kubectl get namespaces

### Create namespace
kubectl create namespace NAMESPACE_NAME
kubectl create -f development.yaml

### Delete namespace
kubectl delete namespace/NAMESPACE_NAME

## Config
### Show config
kubectl config view

### Get current context
kubectl config current-context

### Update context namespace
kubectl config set-context CONTEXT_NAME --namespace=NAMESPACE_NAME

### Define quota for namespace
kubectl apply -f quota.yaml --namespace=NAMESPACE_NAME

### Get used quota by namespace
kubectl get resourcequota RESOURCE_QUOTA_NAME --namespace=NAMESPACE_NAME
kubectl get resourcequota RESOURCE_QUOTA_NAME --namespace=NAMESPACE_NAME --output=yaml

### Get namespace events
kubectl get events -n test

### Get namespace limit range
kubectl get limitrange LIMIT_RANGE_NAME -n test

## ConfigMap

## Create config map

### conf file
$ kubectl create configmap CONFIG_MAP_NAME --from-file=FILE_PATH
$ kubectl create configmap nginx-config --from-file=./nginx.conf

### env file
$ kubectl create configmap CONFIG_MAP_NAME --from-env-file=FILE_PATH
$ kubectl create configmap nginx-config --from-env-file=./.env.dist

### without file
$ kubectl create configmap CONFIG_MAP_NAME --from-literal=KEY=VALUE
$ kubectl create configmap CONFIG_MAP_NAME --from-literal=log_level=WARM

## Get config map
$ kubectl get cm CONFIG_MAP_NAME -o yaml
$ kubectl get cm nginx-config -o yaml

## Edit config map
$ kubectl edit cm/CONFIG_MAP_NAME
$ kubectl create configmap CONFIG_MAP_NAME --from-file=CONF_PATH --dry-run=client -o yaml | kubectl apply -f -

## Secret

### Create secrets
$ kubectl create secret generic SECRET_NAME --from-file=FILE1 --from-file=FILE2
$ kubectl create secret generic SECRET_NAME --from-literal=KEY_NAME=KEY_VALUE
$ kubectl create -f SECRET_YAML_PATH

$ kubectl create secret docker-registry SECRET_NAME \
  --docker-server=SERVER --docker-username=USER --docker-password=PWD --docker-email=EMAIL

### Get secrets
$ kubectl get secrets
$ kubectl get secret SECRET_NAME -o yaml

### Describe secrets
$ kubectl describe secret/SECRET_NAME

## ServiceAccount

### Get service accounts
$ kubectl get sa

## DaemonSet
### get daemon set
$ kubectl get ds

## Job
### Get jobs
$ kubectl get jobs

### Get job logs
$ kubectl logs POD_NAME

### Delete job
$ kubectl delete job JOB_NAME

## CronJob
### Get cron jobs
$ kubectl get cj

### Delete cron job
$ kubectl delete cj CRON_JOB_NAME

## Ingress
### Install Nginx ingress controller
https://kubernetes.github.io/ingress-nginx/deploy/

# Operator

https://github.com/operator-framework/awesome-operators
### CoreOs (GO)
https://github.com/operator-framework/operator-sdk

### ZalandoTech (Python)
https://github.com/zalando-incubator/kopf
https://kopf.readthedocs.io/en/stable/install/

# Volumes
## EmptyDir
Volume lié au pod

## HostPath
Montage d'une ressource de la machine hote dans un pod

## PersistentVolume

### Create a persistent volume
kubectl apply -f PERSISTENT_VOLUME.yaml

### Show persistent volumes
kubectl get pv

## PersistentVolumeClaim

### Create a persistent volume
kubectl apply -f PERSISTENT_VOLUME_CLAIM.yaml

### Show persistent volumes
kubectl get pvc

### StorageClass
kubectl get sc

https://gitlab.com/lucj/k8s-exercices/-/blob/master/Application-Stateful/longhorn.md
kubectl apply -f https://raw.githubusercontent.com/longhorn/longhorn/v1.3.0/deploy/longhorn.yaml

# StatefulSet
Creation d'un cluster

Visualize pod creation
$ kubectl get pods -l app=mysql --watch

Scaling
$ kubectl scale --replicas=4 statefulset/mysql

## ServiceMesh
Proxy a cote du pod qui intercepte toutes les requetes entantes et sortantes
Ca permet de mettre en place des regles de routages, de controle d'acces...
exp : Istio, Linkerd

### Linkerd 
#### Download linkerd binary
$ curl -sL run.linkerd.io/install | sh
$ export PATH=$PATH:$HOME/.linkerd2/bin

#### Check version
$ linkerd version

#### Check if linkerd is ready to be installed into the cluster
$ linkerd check --pre

#### Install in cluster
$ linkerd install --crds | kubectl apply -f -
$ linkerd install | kubectl apply -f -
$ linkerd check

#### Dashboard install
$ linkerd viz install | kubectl apply -f -

#### Dashboard access
$ linkerd viz dashboard &

#### Deploy vote application
$ curl -sL https://run.linkerd.io/emojivoto.yml | kubectl apply -f -
$ kubectl get -n emojivoto deploy -o yaml \
| linkerd inject - \
| kubectl apply -f -
$ linkerd -n emojivoto check --proxy

Delete application
$ curl -sL https://run.linkerd.io/emojivoto.yml \
| kubectl -n emojivoto delete -f -

#### Deploy books application
$ kubectl create ns booksapp
$ curl -sL https://run.linkerd.io/booksapp.yml | kubectl -n booksapp apply -f -
$ kubectl get -n booksapp deploy -o yaml \
| linkerd inject - \
| kubectl apply -f -
$ linkerd -n booksapp check --proxy

##### ServiceProfile
$ curl -sL https://run.linkerd.io/booksapp/webapp.swagger \
| linkerd -n booksapp profile --open-api - webapp \
| kubectl -n booksapp apply -f -

$ curl -sL https://run.linkerd.io/booksapp/authors.swagger \
| linkerd -n booksapp profile --open-api - authors \
| kubectl -n booksapp apply -f -

$ curl -sL https://run.linkerd.io/booksapp/books.swagger \
| linkerd -n booksapp profile --open-api - books \
| kubectl -n booksapp apply -f -

Visualize profiling in console
$ linkerd viz -n booksapp routes deploy/webapp
$ linkerd viz -n booksapp routes svc/webapp
$ linkerd viz -n booksapp routes deploy/webapp --to svc/books

###### Edit ServiceProfile
$ kubectl -n booksapp edit sp/authors.booksapp.svc.cluster.local

Add isRetryable: true in HEAD condition
- condition:
    method: HEAD
    pathRegex: /authors/[^/]*\.json
  isRetryable: true  # Added line
  name: HEAD /authors/{id}.json

$ kubectl -n booksapp edit sp/books.booksapp.svc.cluster.local

Add time out in PUT condition
- condition:
    method: PUT
    pathRegex: /books/[^/]*\.json
  name: PUT /books/{id}.json
  timeout: 25ms # Added line

Delete booksapp
$ curl -sL https://run.linkerd.io/booksapp.yml \
| kubectl -n booksapp delete -f - \
&& kubectl delete ns booksapp


# Helm

## Installation
$ brew install helm
$ helm version
$ helm repo add stable https://charts.helm.sh/stable

## Install chart
$ helm install NAME REPOSITORY
$ helm install nginx stable/nginx-ingress

## List charts
$ helm list

## Delete
$ helm delete nginx

# Dashboard 

$ kubectl apply -f https://raw.githubusercontent.com/kubernetes/dashboard/master/aio/deploy/recommended.yaml
$ kubectl proxy
http://localhost:8001/api/v1/namespaces/kubernetes-dashboard/services/https:kubernetes-dashboard:/proxy/

After creation of ServiceAccount and ClusterRoleBinding
$ echo $(kubectl -n kube-system get secret $(kubectl -n kube-system get secret | grep admin-user | awk '{print $1}') -o jsonpath='{.data.token}') | base64 --decode
Use the token to login to dashboard
# Utils

## ArgoCD
kubectl create namespace argocd
kubectl apply -n argocd -f https://raw.githubusercontent.com/argoproj/argo-cd/stable/manifests/install.yaml
kubectl port-forward svc/argocd-server -n argocd 8080:443

user : admin
password : kubectl -n argocd get secret argocd-initial-admin-secret -o jsonpath="{.data.password}" | base64 --decode

## Kubernetes Server API
https://kubernetes.io/docs/reference/generated/kubernetes-api/v1.24/

## Auto completion
$ sudo apt-get install bash bash-completion
$ source <(kubectl completion bash)

## Rancher 
Solution de gestion des clusters

## Kubeadm
kubeadm : gestion des nodes
WeaveNet : gestion du reseau
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Installation/kubeadm.md

## Minikube
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Installation/minikube.md
https://github.com/kubernetes/minikube

## Kind
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Installation/kind.md
https://github.com/kubernetes-sigs/kind

## Micro8s
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Installation/microK8s.md
https://github.com/canonical/microk8s

## k3s
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Installation/k3s.md
https://github.com/k3s-io/k3s

## k3d
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Installation/k3d.md


# Multipass
https://gitlab.com/lucj/k8s-exercices/-/blob/master/Installation/multipass.md

## Commands

### Create VM
multipass launch -n VM_NAME
multipass launch -n VM_NAME -c 2 -m 3G -d 10G

### Infos VM
multipass info VM_NAME

### List VM
multipass list

### Execute shell
multipass shell node1
multipass exec node1 -- /bin/bash -c "curl -sSL https://get.docker.com | sh"

### Mount / Unmount
local machine : mkdir /tmp/test && touch /tmp/test/hello
VM node1
multipass mount /tmp/test node1:/usr/share/test
multipass exec node1 -- ls /usr/share/test
multipass umount node1:/usr/share/test

### Transfer file
multipass transfer /tmp/test/hello node1:/tmp/hello

### Delete VM
multipass delete -p node1 node2

# References
https://www.cncf.io/
https://katacoda.com
Metal LB => create load balancer for local https://metallb.universe.tf/
https://github.com/stedolan/jq
https://kubernetes.io/fr/docs/reference/kubectl/jsonpath/
https://kubernetes.io/fr/docs/tasks/access-application-cluster/web-ui-dashboard/
https://github.com/ahmetb/kubectl-aliases
https://github.com/ahmetb/krew (create plugins)


kubelet ?
ingress ?
kubectl ?
minikube ?
manifest ?