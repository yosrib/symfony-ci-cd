# ELK

## Fake log generator
mingrammer/flog

### Generate fake log
$ docker run mingrammer/flog -f apache_combined > nginx.log

### Send log to logstash
$ while read -r line; do curl -s -XPUT -d "$line" http://HOST:8080; done < ./nginx.log
