import statistics
import subprocess
from time import *

def get_default_interface():
    try:
        result = subprocess.run(['ip', 'route', 'show', 'default'], capture_output=True, text=True, check=True)

        output_lines = result.stdout.strip().split('\n')
        if output_lines:
            default_route_info = output_lines[0]
            interface = default_route_info.split()[4]
            return interface
        else:
            return None

    except subprocess.CalledProcessError as e:
        print(f"Erreur lors de l'exécution de la commande : {e}")
        return None

def get_bytes(t):
    iface = "eth0"
    default_iface = get_default_interface()
    if default_iface != None:
        iface = default_iface
    with open('/sys/class/net/' + iface + '/statistics/' + t + '_bytes', 'r') as f:
        data = f.read()
        return int(data)

def saveDataFromList(array, filename='web/bandwidth.txt'):
    with open(filename, 'w') as file:
        file.write('\n'.join(map(str, array)))

def readPreviousBandwith():
    values = []
    try:
        with open('web/bandwidth.txt', 'r') as file:
            for line in file:
                value = line.strip() 
                values.append(value)
    except FileNotFoundError:
        print("File not found.")
    return values

def calcData():
    rx1 = get_bytes('rx')
    tx1 = get_bytes('tx')
    sleep(15)
    rx2 = get_bytes('rx')
    tx2 = get_bytes('tx')
    rx_speed = round(((rx2 - rx1)/15)/1000.0, 4)
    tx_speed = round(((tx2 - tx1)/15)/1000.0, 4)

    return rx_speed, tx_speed

def saveData():
    values = readPreviousBandwith()
    dataR, dataT = calcData()
    values.append(str(dataR) + ";" + str(dataT))
    start = len(values) - 3*24*60*4 - 1
    if start < 0:
        start = 0
    values = values[start:]
    saveDataFromList(values)
    print("Saved new data !")

def saveBandwith():
    saveData()
    while True:
        saveData()
        sleep(15)
