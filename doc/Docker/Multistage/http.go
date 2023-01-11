package main

import (
        "io"
        "net/http"
        "os"
)

func handler(w http.ResponseWriter, req *http.Request) {
        host, err := os.Hostname()
        if err != nil {
          io.WriteString(w, "unknown")
        } else {
          io.WriteString(w, host)
        }
}

func main() {
        http.HandleFunc("/whoami", handler)
        http.ListenAndServe(":8080", nil)
}
