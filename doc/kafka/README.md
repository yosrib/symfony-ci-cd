
## Kafka console
Inside the kafka container, you can create a topic, producer and consumer with the kafka shell scripts.

```shell
docker-compose exec kafka sh
```
### Create topic

```shell
kafka-topics.sh --create --bootstrap-server \
  localhost:9092 --replication-factor 1 --partitions 1 --topic test-creation
# Created topic test-creation.
```

### Create producer

```shell
kafka-console-producer.sh --bootstrap-server localhost:9092 --topic test-creation
# wright messages to send to topic
```

### Create consumer
```shell
kafka-console-consumer.sh --bootstrap-server localhost:9092 --topic test-creation
# listen topic, messages sent will be displayed
```