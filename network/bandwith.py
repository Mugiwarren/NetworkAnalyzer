from time import *

def get_bytes(t, iface='wlp0s20f3'):
    with open('/sys/class/net/' + iface + '/statistics/' + t + '_bytes', 'r') as f:
        data = f.read()
        return int(data)

def saveDataFromList(array, filename='data/bandwith.txt'):
    with open(filename, 'w') as file:
        file.write('\n'.join(map(str, array)))

def readPreviousBandwith():
    values = []
    try:
        with open('data/bandwith.txt', 'r') as file:
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
    return values

def saveData():
    values = readPreviousBandwith()
    data = calcData()
    for value in data:
        values.append(value)
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