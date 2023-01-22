# TICK

## generate data
$ docker run lucj/genx:0.1 -type cos -duration 3d -min 10 -max 25 -step 1h > /tmp/data

## send data 
$ chmod +x ./send.sh
$ ./send.sh

## Query in chronograph explore
select "value" from "test"."autogen"."temp"