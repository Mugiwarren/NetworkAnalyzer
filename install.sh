#!/bin/bash

# Check if the script is running with sudo
if [ "$(id -u)" != "0" ]; then
    echo "This script must be run with sudo. Exiting."
    exit 1
fi

# Install required Linux packages if not already installed
sudo apt install python3.8 -y
sudo apt install pip -y
sudo apt install nmap -y
sudo apt install iproute2 -y
sudo apt install screen -y
sudo apt-get install php -y

pip3 install -U threading
pip3 install -U statistics
pip3 install -U subprocess
pip3 install -U os
pip3 install -U socket
pip3 install -U struct
pip3 install -U textwrap
pip3 install -U getmac
pip3 install -U datetime
pip3 install -U time
pip3 install -U python-nmap
