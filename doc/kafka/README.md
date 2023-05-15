
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
You can update `--replication-factor` value if you have multiple broker
> The replication factor should be less or equal the number of broker<br>
> If you have 3 brokers the --replication-factor should be 3 maximum
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
add `--from-beginning` option if you want to receive old messages in the topic