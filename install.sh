#!/bin/bash

# Check if the script is running with sudo
if [ "$(id -u)" != "0" ]; then
    echo "This script must be run with sudo. Exiting."
    exit 1
fi

# Install required Linux packages if not already installed
if ! dpkg -s python3.8 &> /dev/null; then
    echo "Installing python3.8..."
    apt-get -y install python3.8
else
    echo "python3.8 is already installed."
fi

if ! dpkg -s iproute2 &> /dev/null; then
    echo "Installing iproute2..."
    apt-get -y install iproute2
else
    echo "iproute2 is already installed."
fi

if ! dpkg -s nmap &> /dev/null; then
    echo "Installing nmap..."
    apt-get -y install nmap
else
    echo "nmap is already installed."
fi

if ! dpkg -s python3-pip &> /dev/null; then
    echo "Installing python3-pip..."
    apt-get -y install python3-pip
else
    echo "python3-pip is already installed."
fi

# Upgrade pip (optional)
pip3 install --upgrade pip

# Install required Python libraries using pip
if ! python3 -c "import threading" &> /dev/null; then
    echo "Installing threading..."
    pip3 install threading
else
    echo "threading is already installed."
fi

if ! python3 -c "import statistics" &> /dev/null; then
    echo "Installing statistics..."
    pip3 install statistics
else
    echo "statistics is already installed."
fi

if ! python3 -c "import getmac" &> /dev/null; then
    echo "Installing getmac..."
    pip3 install getmac
else
    echo "getmac is already installed."
fi

echo "Setup complete."
