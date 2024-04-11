import json
import statistics
import time as t
import os

from packets_listener import create_log_file

def main():
    data = {}

    config = {
        "request_before_warnings": 1000,
    }
    with open('network/config.json', 'r') as json_file:
        config = json.load(json_file)
    
    print("Loaded config for DDOS detection: " + str(config) + " \n")

    while True:
        NUMBER_OF_PACKETS = 0
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
            NUMBER_OF_PACKETS += 1

        for key in hashMap:
            if hashMap[key][0] > 0:
                hashMap[key][1] = hashMap[key][1] / hashMap[key][0]
            
            if (key in data) == False:
                data[key] = []
            data[key].append((hashMap[key][0], hashMap[key][1]))
        for key in data:
            if (key in hashMap) == False:
                hashMap[key] = []
                hashMap[key].append((0,0))
            
            start_index = len(hashMap[key])-16
            if start_index < 0:
                start_index = 0
            data[key] = data[key][start_index:]

        with open('web/data/cache/request.csv', 'w') as file:
            file.write("")

        start_analysis(data, NUMBER_OF_PACKETS, config)

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
        with open('web/data/cache/request.csv', 'w') as file:
            file.write("")

    except FileNotFoundError:
        print("File not found.")

    return requests

def start_analysis(data, NUMBER_OF_PACKETS, config):

    port_size = {}
    coef_value = 0
    for key in data:
        v = 1
        if key in config['most_used_ports']:
            v = 6
        elif key in config['important_ports']:
            v = 3
        coef_value += v
        port_size[key] = v

    for key in data:
        analysis(key, data[key], NUMBER_OF_PACKETS, config, port_size[key]/coef_value)
    return

def analysis(port, values, NUMBER_OF_PACKETS, config, percentPacketsAllowed):

    print("----------------------------------\nStarting analyse of port: " + str(port))

    data = [values[len(values)-1-i] for i in range(len(values))]

    precedent_values = []
    for i in range(len(data)):
        amount, size = data[i]
        precedent_values.append(amount)

    if len(precedent_values) <= 1:
        print("Impossible to do analysis because there is only one value !")

    if len(data) > 0 and len(precedent_values) > 1:
        print("Values: " + str(len(data)))
        l_a, l_s = data[0]
        savePortValue(port, l_a, l_s)

        detection_number = config['request_before_warnings']
        print("Total number of packets: " + str(NUMBER_OF_PACKETS))
        print("Detection number: " + str(detection_number))
        print("Number of packets: " + str(l_a))
        print("Average size of packets: " + str(l_s))

        if NUMBER_OF_PACKETS > detection_number:

            percent = l_a / NUMBER_OF_PACKETS

            print("Calculated %: " + str(percent*100))
            print("Max allowed: " + str(percentPacketsAllowed*100.0))

            # We're checking if it is greater than the threshold
            if percent >= percentPacketsAllowed:

                # Let's load data from the file
                n = 0
                size_of_packets = 0

                try:
                    with open('web/data/ports/history_' + str(port) + '.csv', 'r') as files:
                        for line in files:
                            splitted = line.split()
                            if len(splitted) == 2:
                                try:
                                    size = int(splitted[1])
                                    size_of_packets += size
                                    n += 1
                                except ValueError:
                                    print('Skipping value: ' + str(line) + ' while reading !')
                except FileNotFoundError:
                    print("File not found.")
                
                if n > 0:
                    size_of_packets = size_of_packets / n

                #
                # WARNING SUR LA TAILLE DES PACKETS TRANSMIS
                #

                # We're according 50% of error for the size of the packet
                step = size_of_packets / 2
                less_value = size_of_packets - step
                great_value = size_of_packets + step

                print("Less value: " + str(less_value))
                print("Max value: " + str(great_value))
                print("Value calculted: " + str(l_s))

                if l_s < less_value:
                    create_log_file("WARNING", "PORT " + str(port), "The size of packet received on port is anormally small.")
                elif l_s > great_value:
                    create_log_file("WARNING", "PORT " + str(port), "The size of packet received on port is anormally big.")
                
                #
                # WARNING SUR LE NOMBRE DE REQUETES
                #

                # Check if the value is abnormal
                if is_outlier(precedent_values, l_a):
                    print("\nWarning: Abnormal value detected !")
                    # checking if error is near predicted value with 20% of error
                    if is_predictable(precedent_values, l_a, 0.2) == False:
                        print("\nValue was not predictable.\n")
                        create_log_file("WARNING", "PORT " + str(port), "Receiving unexpected huge amount of request on port " + str(port))

        

    return

def is_predictable(data, recent_value, max_relative_error):
    # Calculate the linear trend based on previous data points
    n = len(data)
    print("")
    print("     Length of data:", len(data))
    
    if n < 2:
        return False  # Need at least two data points to estimate a trend
    
    # Calculate the differences (variations) between consecutive data points
    differences = [data[i] - data[i - 1] for i in range(1, n)]
    
    # Calculate the mean difference (approximating the linear trend)
    mean_difference = sum(differences) / (n - 1)
    print("     Mean difference: ", mean_difference)
    
    # Predict the next value by extrapolating the linear trend
    predicted_next_value = data[-1] + mean_difference
    print("     Predicted next value: ", predicted_next_value)
    # Calculate the absolute and relative errors between the recent value and the predicted next value
    absolute_error = abs(recent_value - predicted_next_value)
    relative_error = absolute_error / predicted_next_value
    print("     Absolute error:", absolute_error)
    print("     Relative error:", relative_error)
    
    # Check if the relative error is within the specified threshold to consider the recent value predictable
    if relative_error <= max_relative_error:
        return True
    else:
        return False

def is_outlier(data, value):
    # Calculate the mean and standard deviation of the data
    print("")

    print("     Length of data:", len(data))
    mean_value = statistics.mean(data)
    print("     Mean value:", mean_value)
    std_dev = statistics.stdev(data)
    print("     Standart deviation:", std_dev)
    
    # Calculate the z-score for the given value
    z_score = 9999999999
    if std_dev > 0:
        z_score = (value - mean_value) / std_dev
    elif std_dev == 0:
        z_score = 0
    
    print("     Z-score:", z_score)
    # Define a threshold for determining if the value is an outlier
    outlier_threshold = 2  # Typical threshold for z-score
    
    # Check if the absolute z-score exceeds the outlier threshold
    if z_score > outlier_threshold:
        return True
    else:
        return False

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