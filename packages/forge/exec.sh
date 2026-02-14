#!/bin/bash

set -e

command=$1
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

start() {
    docker compose -f "$SCRIPT_DIR/docker-compose.yml" up --build -d
}

attach() {
    docker compose -f "$SCRIPT_DIR/docker-compose.yml" exec app bash
}

stop() {
    ocker compose -f "$SCRIPT_DIR/docker-compose.yml" stop
}

down() {
    ocker compose -f "$SCRIPT_DIR/docker-compose.yml" down
}

if [[ "$command" = "" ]]; then
    echo "You need specify most one command, try './vendor/aegis/forge/exec.sh help' for more information"
    exit 1
fi

if [[ "$command" = "start" ]]; then
    start
    exit 1
fi

if [[ "$command" = "attach" ]]; then
    attach
    exit 1
fi

if [[ "$command" = "stop" ]]; then
    stop
    exit 1
fi
