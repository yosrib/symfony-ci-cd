
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