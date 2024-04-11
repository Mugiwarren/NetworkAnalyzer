if [ "$(id -u)" != "0" ]; then
    echo "This script must be run with sudo. Exiting."
    exit 1
fi

sudo sh stop.sh
sudo sh start.sh