var express = require('express');
var app = express();
app.get('/ping', function(req, res) {
    console.log("received");
    res.setHeader('Content-Type', 'text/plain');
    console.log("ping-pong");
    res.end("PONG");
});
app.listen(80);
