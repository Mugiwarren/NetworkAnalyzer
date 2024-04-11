#!/bin/bash

if [ "$(id -u)" != "0" ]; then
    echo "This script must be run with sudo. Exiting."
    exit 1
fi

if screen -list | grep -q network_analyzer_python; then
    echo "Terminating existing screen session: network_analyzer_python"
    screen -S network_analyzer_python -X quit
fi


if screen -list | grep -q network_analyzer_php; then
    echo "Terminating existing screen session: network_analyzer_php"
    screen -S network_analyzer_php -X quit
fi
