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

# Utils
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

# Utils 

## Auto completion 
$ sudo apt-get install bash bash-completion
$ source <(kubectl completion bash)