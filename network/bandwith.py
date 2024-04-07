import statistics
import subprocess
from time import *

def get_default_interface():
    try:
        # Exécute la commande ip route pour obtenir la route par défaut
        result = subprocess.run(['ip', 'route', 'show', 'default'], capture_output=True, text=True, check=True)

        # Analyse la sortie pour obtenir l'interface
        output_lines = result.stdout.strip().split('\n')
        if output_lines:
            default_route_info = output_lines[0]
            interface = default_route_info.split()[4]  # Obtient l'interface à partir de la sortie
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

def saveDataFromList(array, filename='web/data/bandwith.txt'):
    with open(filename, 'w') as file:
        file.write('\n'.join(map(str, array)))

def readPreviousBandwith():
    values = []
    try:
        with open('web/data/bandwith.txt', 'r') as file:
            for line in file:
                value = float(line.strip())  # Convert each line to an integer
                values.append(value)
    except FileNotFoundError:
        print("File not found.")
    return values

def calcData():
    rx1 = get_bytes('rx')
    values = []
    for i in range(15):
        sleep(1)
        rx2 = get_bytes('rx')
        rx_speed = round((rx2 - rx1)/1000.0, 4)
        values.append(rx_speed)
        rx1 = rx2
    return statistics.mean(values)

def saveData():
    values = readPreviousBandwith()
    data = calcData()
    values.append(data)
    start = len(values) - 3*24*60*60 - 1
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