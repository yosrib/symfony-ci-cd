# Without multistage
#FROM golang:1.17
#WORKDIR /go/src/app
#COPY http.go .
#RUN go mod init
#RUN CGO_ENABLED=0 GOOS=linux go build -o http .
#CMD ["./http"]

# With multistage
FROM golang:1.17 as build
WORKDIR /go/src/app
COPY http.go .
RUN go mod init
RUN CGO_ENABLED=0 GOOS=linux go build -o http .

FROM scratch
COPY --from=build /go/src/app .
CMD ["./http"]
