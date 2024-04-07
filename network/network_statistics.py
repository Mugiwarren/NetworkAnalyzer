import time as t
import os

def main():
    data = {}
    while True:
        t.sleep(15)
        hashMap = {}
        traffic = loadData()
        for request in traffic:
            port = request[0]
            size = request[1]
            if (port in hashMap) == False:
                hashMap[port] = [0, 0]
            hashMap[port][0] = hashMap[port][0] + 1
            hashMap[port][1] = hashMap[port][1] + size

        for key in hashMap:
            if hashMap[key][0] > 0:
                hashMap[key][1] = hashMap[key][1] / hashMap[key][0]
            
            if (key in data) == False:
                data[key] = []
            data[key].append((hashMap[key][0], hashMap[key][1]))
        
        for key in data:
            if (key in hashMap) == False:
                data[key].append((0,0))
            
            start_index = len(hashMap[key])-16
            if start_index < 0:
                start_index = 0
            data[key] = data[key][start_index:]

        with open('web/data/cache/request.csv', 'w') as file:
            file.write("")

        start_analysis(data)

def loadData():
    requests = []
    try:
        with open('web/data/cache/request.csv', 'r') as file:
            for l in file:
                l = l.replace('\n', '')
                l_splitted = l.split(';')
                if len(l_splitted) == 2:
                    requests.append([int(l_splitted[0]), int(l_splitted[1])])

    except FileNotFoundError:
        print("File not found.")
    
    try:
        with open('data/cache/request.csv', 'w') as file:
            file.write("")

    except FileNotFoundError:
        print("File not found.")

    return requests

def start_analysis(data):
    for key in data:
        analysis(key, data[key])
    return

def analysis(port, values):

    print("ANALYSE DU PORT " + str(port) + " EN COURS !!!")

    data = [values[len(values)-1-i] for i in range(len(values))]
    if len(data) > 0:
        l_a, l_s = data[0]
        savePortValue(port, l_a, l_s)

        percentOfInvalidPackets = 0
        

    return

def savePortValue(port, amountOfRequests, SizeOfRequests):
    if os.path.exists("web/data/ports/") == False:
        os.makedirs("web/data/ports/")
    file_name = "web/data/ports/history_" + str(port) + ".csv"
    if os.path.exists(file_name) == False:
        with open(file_name, 'w') as file:
            file.write("")
    with open(file_name, 'a') as file:
        file.write(str(amountOfRequests) + ";" + str(SizeOfRequests) + "\n")


def init_stats():
    main()