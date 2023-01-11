FROM node:19-alpine3.16
# OR
#FROM alpine:3.16
#RUN apk add -u npm

WORKDIR /app

# Install dependecies here to use cache (best for building fast)
COPY package.json package.json
RUN npm install

COPY pong.js pong.js

EXPOSE 80

CMD ["npm", "start"]
