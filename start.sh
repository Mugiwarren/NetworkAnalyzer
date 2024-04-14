#!/bin/bash

if [ "$(id -u)" != "0" ]; then
    echo "This script must be run with sudo. Exiting."
    exit 1
fi

if screen -list | grep -q network_analyzer_python; then
    echo "Terminating existing screen session: network_analyzer_python"
    screen -S network_analyzer_python -X quit
fi

echo "Launching Python script in a new screen session: network_analyzer_python"
echo "You can correct python version below this line"
screen -S network_analyzer_python -d -m sudo python3.8 network/backend.py

echo "Script execution initiated in screen session: network_analyzer_python"


if screen -list | grep -q network_analyzer_php; then
    echo "Terminating existing screen session: network_analyzer_php"
    screen -S network_analyzer_php -X quit
fi

echo "Launching Python script in a new screen session: network_analyzer_php"
screen -S network_analyzer_php -d -m php -S localhost:25000 -t web/

echo "Script execution initiated in screen session: network_analyzer_php"
