FROM alpine:3.18

## INSTALL REQUIREMENTS
RUN apk add --no-cache bash openjdk11-jre
ENV JAVA_HOME "/usr"

## INSTALL KAFKA
RUN wget https://dlcdn.apache.org/kafka/3.4.0/kafka_2.13-3.4.0.tgz
RUN tar -zxf kafka_2.13-3.4.0.tgz
RUN cp -R kafka_2.13-3.4.0 /opt/kafka/

## CLEAN
RUN rm -rf kafka_2.13-3.4.0.tgz

## EXPOSE PORTS
EXPOSE 9092 9094

WORKDIR /opt/kafka

## ADD KAFKA BINARY DIR TO PATH
ENV PATH=/opt/kafka/bin:$PATH

## COPY KAFKA CONF
RUN mkdir kafka-logs
COPY zookeeper.properties ./config/

## DELETE WINDOWS CONF
RUN rm -rf /opt/kafka/bin/windows

## DEFINE VOLUMES
VOLUME ["/opt/kafka/config", "/opt/kafka/kafka-logs"]

## START KAFKA SERVICE
ENTRYPOINT ["kafka-server-start.sh", "/opt/kafka/config/server.properties"]
