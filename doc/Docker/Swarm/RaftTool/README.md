
# Swarm Rafttool
Un binaire qui se trouve dans la librairie SwarmKit utilisée par Docker pour la gestion des clusters Swarm.

## Decrypt logs

$ docker run --rm -ti --entrypoint='' \
-v /var/lib/docker/swarm/:/var/lib/docker/swarm:ro \
-v $(pwd):/tmp/ lucj/swarm-rafttool:1.0 sh


- Create dump.sh
/go # cat<<'EOF' | tee dump.sh
  d=$(date "+%Y%m%dT%H%M%S")
  SWARM_DIR=/var/lib/docker/swarm
  WORK_DIR=/tmp
  DUMP_FILE=$WORK_DIR/dump-$d
  STATE_DIR=$WORK_DIR/swarm-$d
  cp -r $SWARM_DIR $STATE_DIR
  $GOPATH/bin/swarm-rafttool dump-wal --state-dir $STATE_DIR > $DUMP_FILE
  echo $DUMP_FILE
  EOF

/go # chmod +x ./dump.sh
/go # ./dump.sh | xargs cat
