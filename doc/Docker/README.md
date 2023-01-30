# Utils
https://labs.play-with-docker.com/
https://hub.docker.com/r/dockersamples/visualizer
https://github.com/stefanprodan/swarmprom/blob/master/docker-compose.yml
https://www.portainer.io/
TICK Stack https://wiki.archlinux.org/title/TICK_stack
Harbor registry https://github.com/goharbor/harbor
ELK https://www.elastic.co/fr/what-is/elk-stack
https://swarmpit.io/
Cloud logging : https://www.sumologic.com/

## Images
VotingApp 
https://github.com/dockersamples/example-voting-app
https://gitlab.com/voting-application
instavote/vote:latest
instavote/vote:indent
City lucj/city:1.0
Fake logs mingrammer/flog
lucj/whoami:1.0
Random logger
chentex/random-logger

# Namespaces
pid: isolation de l'espace des processus
net: donne une stack reseau prive
mount: system de fichier prive
uts: nom du host
ipc: isole les communication inter processus
user: mapping UID/GID entre l'hote et les contenaires

# Installation 
$ curl -sSL https://get.docker.com | sh
$ sudo usermod -aG docker USER
$ docker version
restart instance session

# Play with docker
https://labs.play-with-docker.com/

# Docker Demon
$ dockerd -h
$ ps aux | grep dockerd
$ sudo systemctl status docker
$ cat /lib/systemd/system/docker.service
$ cat /lib/systemd/system/docker.socket

## Client

## Server
$ sudo systemctl stop docker
$ sudo dockerd -H 0.0.0.0:2375

# Container
## Foreground
$ docker container run nginx
$ docker container run alpine ping 8.8.8.8
## Background
$ docker container run -d nginx

## Bind mount
$ docker container run -v HOST_PATH:CONTAINER_PATH
$ docker container run --bind type=bind,src=HOST_PATH,dst=CONTAINER_PATH

### Bind socket
Create admin container
$ docker container run --name admin -ti -v /var/run/docker.sock:/var/run/docker.sock alpine

Create container from the admin container
$ curl -XPOST --unix-socket /var/run/docker.sock -d '{"Image":"nginx:1.12.2"}' -H 'Content-Type:application/json' http://localhost/containers/create

Execute container from the admin container
$ curl -XPOST --unix-socket /var/run/docker.sock  http://localhost/containers/CONTAINER_ID/start

### Read only
docker run -v /:/host:ro alpine
ro => read only

## Resource limit
### RAM
$ docker container run --memory MEMORY_LIMIT IMAGE
$ docker container run --memory 32m estesp/hogit
Soft limit 2g and Hard limit 4g
$ docker container run --memory 4g --memory-reservation 2g -d redis

### CPU
$ docker container run --cpus CPU_LIMIT progrium/stress --cpu 4
$ docker container run --cpus 0.5 progrium/stress --cpu 4
Use the CPU corps number 0 and 3
$ docker container run --cpuset-cpus 0,3 ...

## Permissions
--user

## Util options
### Container name
--name
`$ docker container run --name debug alpine sleep 1000`
### Remove container when it's stopped
--rm
`$ docker container run --rm alpine sleep 1000`

### Restart container
--restart
`$ docker container run --restart=on-failure lucj/api`

### Inspect
$ docker container inspect --format '{{ .NetworkSettings.IPAddress }}' CONTAINER_ID
$ docker container inspect --format '{{ json .State }}' CONTAINER_ID | jq

### Logs
$ docker container logs CONTAINER_ID
$ docker container logs -f CONTAINER_ID (real time)

### List
display only containers ID
$ docker container ls -q
display all container (include stopped)
$ docker container ls -a

## Docker directory
/var/lib/docker

Command when used docker for mac
$ docker run -it --privileged --pid=host debian nsenter -t 1 -m -u -n -i sh

# Image
## Dockerfile
FROM
ENV
COPY / ADD
RUN
EXPOSE
VOLUME
USER
HEALTHCHECK
`FROM node:8.11-alpine
RUN apk update && apk add curl
HEALTHCHECK --interval=5s --timeout=3s --retries=3 CMD curl -f localhost:8000/health || exit`
ENTRYPOINT / CMD
## Create image from container
$ docker commit CONTAINER IMAGE_NAME
## Save/Load image 
### Save docker image in a .tar format
docker save -o TAR_NAME.tar IMAGE_NAME:TAG
### Load image
docker load < TAR_NAME.tar
## Multistage build

## Inspect image 
Go template
$ docker inspect --format '{{ json .ContainerConfig.ExposedPorts }}' mongo:3.6 | jq

## Image history
show image build steps 
$ docker image history mongo:3.6

# Registry
## insecure registry
create file /etc/docker/daemon.json with content
`{
"insecure-registries" : ["registry.mydom.com:5000"]
}
`
$ sudo systemctl restart docker
## TLS
[registry vm] Create registry.cnf
`$ openssl req -x509 -newkey rsa:4096 -nodes -sha256 -days 365 \
-keyout certs/domain.key \
-out certs/domain.crt \
-config registry.cnf`

[registry vm] Create registry
`$ docker run -d \
--restart=always \
--name registry \
-v "$(pwd)"/certs:/certs \
-e REGISTRY_HTTP_TLS_CERTIFICATE=/certs/domain.crt \
-e REGISTRY_HTTP_TLS_KEY=/certs/domain.key \
-p 5000:5000 \
registry:2`

[local vm] create /etc/docker/certs.d/registry.mydom.com:5000 directory

[registry vm] Create registry with https
`$ docker run -d \
--restart=always \
--name registry \
-v "$(pwd)"/certs:/certs \
-e REGISTRY_HTTP_TLS_CERTIFICATE=/certs/domain.crt \
-e REGISTRY_HTTP_TLS_KEY=/certs/domain.key \
-e REGISTRY_HTTP_ADDR=0.0.0.0:443 \
-p 443:443 \
registry:2`
[local vm] create /etc/docker/certs.d/registry.mydom.com directory and copy ca.crt

# Storage
Docker storage layers 
/var/lib/docker

Get container mounts directories
$ docker container inspect -f '{{json .Mounts }}' CONTAINER_NAME | jq

# Volume
## Drivers
plugin to create volume via ssh (outside host machine)
$ docker plugin install vieux/sshfs
Create volume via ssh with the driver vieux/sshfs (not recommended to use this plugin in prod)
$ ssh USER@HOST mkdir /tmp/data
$ docker volume create -d vieux/sshfs -o sshcmd=USER@HOST:/tmp/data -o password=PASSWORD VOLUME_NAME

# Docker compose
## VotingApp
https://gitlab.com/voting-application

## Scale
$ docker compose up -d --scale SERVICE_NAME=2

# Docker Swarm

## Distributed consensus
http://thesecretlivesofdata.com/raft/
RAFT est un protocole d'implementation de consensus distribué

## Create new swarm cluster
docker swarm init
node1: $ docker swarm init --advertise-addr 129.168.99.100

## Node
### List nodes
(manager) node: docker node ls

### Add a new node
docker swarm join
(worker) node2: $ docker join --token TOKEN ADDRESS:PORT

### Add node manager to cluster
(manager) node1: $ docker swarm join-token manager
(worker) node3: $ docker join --token TOKEN ADDRESS:PORT

### Destitution node manager to worker
(manager) node1: docker node demote node1
=> node 1 will transform to a worker node

### Promote node worker to manager
(manager) node3: docker node promote node2
=> node 1 will transform to a manager node

### Node Availability
#### Pause
Can't add new task and actual tasks will continue
(manager) node1: $ docker node update --availability pause node2
#### Drain
Transfer alla tasks in another node
(manager) node1: $ docker node update --availability drain node2
#### Active
Active node normally
(manager) node1: $ docker node update --availability active node2

### Node Label
List node labels
$ docker node inspect -f '{{ json .Spec.Labels }}' node1

Add label
$ docker node update --label-add Memcached=true node1

## Service
### Create
$ docker service create --name www -p 8080:80 --replicas 3 nginx

Service to visualize containers in cluster
$ docker service create \
--name visualizer \
--mount type=bind,source=/var/run/docker.sock,destination=/var/run/docker.sock \
--constraint 'node.role == manager' \
--publish "8000:8080" dockersamples/visualizer:stable

### List
$ docker service ls
$ docker service ps www
### Scale
$ docker service scale www=4

### Rolling update
$ docker service create --update-parallelism 2 --update-delay 5s -p 80:80 --replicas 4 --name vote instavote/vote
$ docker service update --image instavote/vote:indent vote

### Rollback
$ docker service rollback SERVICE_NAME

Rollback automatic
docker service create --name whoami --replicas 2 --update-failure-action rollback --update-delay 10s --update-monitor 10s -p 8000:8000 lucj/whoami:1.0

### Secret
#### Create secret
$ echo "PASSWORD" | docker secret create password -

#### Add secret to a service
$ docker service update --secret-add=SECRET_NAME SERVICE_NAME
$ docker create service --name=api --secret=SECRET_NAME IMAGE_NAME
Secret location in container: /run/secrets/SECRET_NAME

## Stack
### Deploy stack
$ docker stack deploy -c COMPOSE.YAML STACK_NAME

#### Scale container
deploy:
    mode: replicated
    replicas: NUMBER

#### Create container in each node
deploy:
    mode: global

#### Placement
Create container only in nodes with linux OS
deploy:
    mode: global
    placement:
        constraints: [node.platform.os == linux]

Create container only in manager nodes
deploy:
    mode: replicated
    replicas: 1
    placement:
        constraints: [node.role == manager]

### List stacks
$ docker stack ls

### List stack services
$ docker stack services STACK_NAME

## Autolock (security)
### Show swarm private key
$ sudo cat /var/lib/docker/swarm/certificates/swarm-node.key
### Lock the swarm
$ docker swarm update --autolock=true
SWMKEY-1-cixdJrRSUaj8Fe35RmPIU3M8dV+0BzW6SoVKzCRp41c
### Unlock swarm
$ docker swarm unlock
Past the key

## Cluster Backup
(cluster 1) $ sudo systemctl stop docker
(cluster 1) Copy /var/lib/docker/swarm in local storage
(cluster 1) $ sudo systemctl start docker

(cluster 2) $ sudo systemctl stop docker
(cluster 2) $ rm -rf /var/lib/docker/swarm
(cluster 1) Copy the backup of (cluster 1)/var/lib/docker/swarm in (cluster 2)/var/lib/docker/swarm
(cluster 2) $ sudo systemctl start docker
(cluster 2) $ docker swarm init --force-new-cluster

# Network
## Create 
$ docker network create --driver DRIVER_NAME NET_NAME
docker native driver list = (host, none, bridge, overlay, macvlan)
overlay : cluster network type (exp: swarm)

### Crypt request/response
$ docker network create --opt encrypted --driver overlay mynet

## Util commands
### List network interface
$ ip a
$ ip a show docker0

### List network in host machine
$ ip link
$ ip link show docker0

### List interfaces by network
$ brctl show
$ brctl show docker0

### List network interfaces IN/OUT 
$ sudo iptables -t nat -nvL

### List network namespaces
$ sudo ls /var/run/docker/netns

### List interfaces of a namespace
exp ingress_sbox namespace
$ sudo nsenter --net=/var/run/docker/netns/ingress_sbox ip a

### Pattern of a request in a namespace
exp ingress_sbox namespace
$ sudo nsenter --net=/var/run/docker/netns/ingress_sbox iptables -t nat -nvL

### List mangle table to see updated request package 
exp ingress_sbox namespace
$ sudo nsenter --net=/var/run/docker/netns/ingress_sbox iptables -t mangle -nvL

### See IPVS configuration
$ sudo nsenter --net=/var/run/docker/netns/ingress_sbox ipvsadm -L


## Inspect container network with Go template
$ docker container inspect -f "{{ json .NetworkSettings.Networks }}" c1 | jq .
$ docker network inspect -f "{{ json .Containers }}" bridge | jq .

# Security
## Hardening
https://www.cisecurity.org/cis-benchmarks/
https://github.com/docker/docker-bench-security

## Capabilities
### List of capabilities
CAP_CHOWN : update uid/gid
CAP_SYS_ADMIN : update system information
CAP_NET_ADMIN : update network information
CAP_NET_BIND_SERVICE : assign privileged port to a process (<1024)
### Add capability
--cap-add
#### exp add SYS_ADMIN to update container hostname
$ docker run -ti --cap-add=SYS_ADMIN alpine sh
(container) $ hostname newname

### Remove capability
--cap-drop
#### Exp remove NET_RAW to disable network traffic
$ docker run -ti --cap-drop=NET_RAW alpine sh
=> we can't ping an IP

## LSM Linux Security Modules
### LSM : AppArmor
$ docker run -ti alpine sh
(container) $ cat /proc/kcore => permission denied
$ docker run -ti --security-opt apparmor:uncofigured alpine sh
(container) $ cat /proc/kcore => cat OK

### LSM Linux Security Modules : SELinux
Control system access  https://opensource.com/business/13/11/selinux-policy-guide
security context associated to subject and object (context => who can do)
$ docker run  -v /:/host -ti alpine sh
(container) $ echo "test" > /host/usr/share/nginx/html/index.html
=> permission denied
$ docker run  -v /:/host --security-opt label:disable -ti alpine sh
(container) $ echo "test" > /host/usr/share/nginx/html/index.html
=> operation OK

## SECCOMP
manage linux process (mkdir, ...)

$ cat policy.json
```json
{
  "defaultAction": "SCMP_ACT_ALLOW",
  "syscalls": [
    {
      "name": "mkdir",
      "action": "SCMP_ACT_ERRNO"
    }
  ]
}
```
$ docker run -it --security-opt seccomp:policy.json alpine sh
=> mkdir not permitted

## Scanning CVE
### Some solutions
docker security scanning
Anchore Engine
Clair
Trivy https://aquasecurity.github.io/trivy/v0.18.3/
...

## Content trust
export DOCKER_CONTENT_TRUST=1 before build image

## Commercial solution
aqua security
NeuVector
SysDig Monitor
TwistLock
BlackDuck

# Log
## Docker service failed start log
$ docker service ps --no-trunc {serviceName}
$ journalctl -u docker.service | tail -n 50

## Drivers list
https://docs.docker.com/config/containers/logging/local/
```bash
$ docker info | grep Log
```

| Driver     | Logs destination                          |
|------------|-------------------------------------------|
| json-file  | local (default)                           |
| awslogs    | service AWS CloudWatch                    |
| gcplogs    | GCP                                       |
| logentries | https://logentries.com                    |
| splunk     | https://splunk.com                        |
| gelf       | endpoint GELF (exp: logstash, graylog...) |
| syslog     | daemon syslog (host machine)              |
| fluentd    | daemon fluentd (host machine)             |
| journald   | daemon journald (host machine)            |

## Update default docker logs driver
### Update docker daemon
```json
{
  "log-driver": "gelf",
  "log-opts": {
    "gelf-address": "udp://1.2.3.4:12201"
  }
}
```
### For specific container
#### Command line
```bash
$ docker run \
  --log-driver gelf --log-opt gelf-address=udp://1.2.3.4:12201 \
  alpine echo hello world
```

#### Docker compose
```yaml
services:
  reverse-proxy:
    image: alpine
    logging:
      driver: gelf
      options:
        gelf-address: udp://1.2.3.4:12201
```

## Sumologic cloud logging
https://github.com/SumoLogic/sumologic-docker-logging-driver#step-1-configure-sumo-to-receive-docker-logs
### install driver
$ docker plugin install sumologic/docker-logging-driver:1.0.6 --alias sumologic --grant-all-permissions
### Create daemon
create file /etc/docker/daemon.json
```json
{
  "log-driver": "sumologic",
  "log-opts": {
    "sumo-url": "https://<deployment>.sumologic.com/receiver/v1/http/<source_token>"
  }
}
```
