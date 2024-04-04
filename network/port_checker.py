import time
import nmap

def saveOpenPorts():
    open_ports = scan_ports()
    save_open_ports_to_file(open_ports)
    print("Open ports:", open_ports)

def save_open_ports_to_file(open_ports, filename='data/ports.txt'):
    with open(filename, 'w') as file:
        file.write('\n'.join(map(str, open_ports)))

def scan_ports():
    nm = nmap.PortScanner()
    nm.scan('127.0.0.1', arguments='-p 1-65535 --open')  # Scan all ports from 1 to 65535 for open ports
    open_ports = []

    for host in nm.all_hosts():
        for proto in nm[host].all_protocols():
            ports = nm[host][proto].keys()
            for port in ports:
                if nm[host][proto][port]['state'] == 'open':
                    open_ports.append(port)

    return open_ports


def start():

    while True:
        saveOpenPorts()
        print("Ports has been refreshed")
        time.sleep(30)
