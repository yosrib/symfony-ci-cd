
## Kafka console
Inside the kafka container, you can create a topic, producer and consumer with the kafka shell scripts.

```shell
docker-compose exec kafka sh
```

## Topic
### Create topic
Create topic in single broker
```shell
kafka-topics.sh --create --bootstrap-server localhost:9092 \
  --replication-factor 1 --partitions 1 --topic TOPIC_NAME
# Created topic TOPIC_NAME.
```
Create topic with replication<br>
`--bootstrap-server`  list of brokers hosts separate with comma<br>
`--replication-factor` number of replication (max = number of brokers)
```shell
kafka-topics.sh --create --bootstrap-server localhost:9092,kafka-2:9092,kafka-3:9092 \
  --replication-factor 3 --partitions 1 --topic TOPIC_NAME
# Created topic TOPIC_NAME.
```
Create topic in multiple partitions<br>
`--partitions` number of partitions
```shell
kafka-topics.sh --create --bootstrap-server kafka-1:9092,kafka-2:9092,kafka-3:9092 \
  --replication-factor 3 --partitions 2 --topic TOPIC_NAME
# Created topic TOPIC_NAME.
```

### List topics
```shell
kafka-topics.sh --list --bootstrap-server localhost:9092,localhost:9093,localhost:9094
```

### Describe topic 
```shell
kafka-topics.sh --describe --bootstrap-server localhost:9092,kafka-2:9092,kafka-3:9092 --topic TOPIC_NAME
```

### Delete all messages
Create json file `delete-all.config`
```json
{
  "partitions": [
    {
      "topic": "TOPIC_NAME",
      "partition": 0,
      "offset": -1
    }
  ],
  "version": 1
}
```
```shell
kafka-delete-records.sh --bootstrap-server kafka:9092 \
  --offset-json-file delete-all.config
```

You can update `--replication-factor` value if you have multiple broker
> The replication factor should be less or equal the number of broker<br>
> If you have 3 brokers the --replication-factor should be 3 maximum
## Create producer
Create message with value
```shell
kafka-console-producer.sh --bootstrap-server localhost:9092 --topic test-creation
# wright messages to send to topic
```
Create message with key and value<br>
`--property parse.key=true` to accept message key<br>
`--property key.separator=,` define the separator between key and value `key,value`
```shell
kafka-console-producer.sh --bootstrap-server localhost:9092 --topic topic-multiple-broker-2 \
  --property parse.key=true --property key.separt
or=,
```

## Consumer
### Create consumer
```shell
kafka-console-consumer.sh --bootstrap-server localhost:9092 --topic TOPIC_NAME
# listen topic, messages sent will be displayed
```
add `--from-beginning` option if you want to receive old messages in the topic<br>
add `--property print.key=true` to get messages with key<br>
add `--property print.timestamp=true` to get messages with timestamp<br>
add `--partition PARTITION_NUMBER` to consume only the messages of the partition specified `PARTITION_NUMBER`<br>
add `--offset PARTITION_NUMBER` to consume only the messages from the offset specified<br>
> `--offset` work only with `--partition` option and without `--from-beginning` option<br>

**Serialisation / Deserialization**<br>
add `--property key.deserializer=DESERIALIZER` to deserialize key
add `--property value.deserializer=DESERIALIZER` to deserialize value

```shell
afka-console-consumer.sh --bootstrap-server kafka-1:9092 --topic topic-multiple-broker-2 --from-beginning \
  --property print.key=true --property key.deserializer=org.apache.kafka.common.serialization.StringDeserializer \
  --property value.deserializer=org.apache.kafka.common.serialization.StringDeserializer
```

### Create consumer groups
List the consumer groups
```shell
kafka-consumer-groups.sh --bootstrap-server kafka-1:9092,kafka-2:9092,kafka-3:9092 --list
```
Create a consumer groups
```shell
kafka-console-consumer.sh --bootstrap-server kafka-1:9092,kafka-2:9092,kafka-3:9092 --topic topic-multiple-broker-2  \
  --group udemy --from-beginning
```
### Describe consumer groups
```shell
kafka-consumer-groups.sh --describe --bootstrap-server kafka-1:9092,kafka-2:9092,kafka-3:9092 --group GROUP_NAME
```

### Reset offset
```shell
kafka-consumer-groups.sh --bootstrap-server kafka-1:9092,kafka-2:9092,kafka-3:9092 --group GROUP_NAME --reset-offsets \
  --to-earliest --all-topics --dry-run
```
add `--dry-run` to show the result of the command without execute it<br>
replace `--dry-run` with `--execute` to execute the reset offsets<br>
`--to-earliest` place the consumer in the earliest offset<br>
`--to-latest` place the consumer in the latest offset<br>
`--to-offset OFFSET_NUMBER` place the consumer in a specific offset<br>
`--to-datetime 2023-05-15T00:00:00.000`<br>

## Kafka connect
### Mode
Standalone mode for development env<br>
Distributed mode for production env<br>
[Kafka connect hub](https://confluent.io/hub)

#### Standalone
Execute a standalone connect with source connect (FileStreamSource)
```shell
connect-standalone.sh KAFKA_CONNECT_CONFIG SOURCE_COSOURCE_CONNECTOR_CONFIGNFIG
```
```shell
connect-standalone.sh ./connect/kafka-connect-standalone.properties ./connect/source-connect.properties
```
when the source is updated (in our example the source is text file stream.txt), kafka connect retrieve the text from the file and send there to the topic
If we execute a consumer to listen the topic we will show the messages displayed
```shell
kafka-console-consumer.sh --bootstrap-server kafka-1:9092,kafka-2:9092,kafka-3:9092 --topic stream-text-connect
```
Execute a standalone connect with source & sink connect (FileStreamSink)
```shell
connect-standalone.sh KAFKA_CONNECT_CONFIG SOURCE_CONNECTOR_CONFIG SINK_CONNECTOR_CONFIG
```
```shell
connect-standalone.sh ./connect/kafka-connect-standalone.properties \
  ./connect/source-connect.properties \
  ./connect/sink-connect.properties
```
### Transformer
[SMT](https://docs.confluent.io/platform/current/connect/transforms/maskfield.html) : Simple Message Transform <br>
Configure the transformer in source or sink connector.
```properties
transforms=TransformNameOne,TransformNameTwo

# TransformNameOne: MaskField
transforms.TransformNameOne.type=org.apache.kafka.connect.transforms.MaskField$Value
transforms.TransformNameOne.fields=lastName
# TransformNameTwo: InsertField
transforms.TransformNameTwo.type=org.apache.kafka.connect.transforms.InsertField$Value
transforms.TransformNameTwo.static.field=topic
transforms.TransformNameTwo.static.value=stream-text-connect
```
