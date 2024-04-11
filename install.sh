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

pip install threading
pip install statistics
pip install subprocess
pip install os
pip install socket
pip install struct
pip install textwrap
pip install getmac
pip install datetime
pip install time
pip install nmap