import socket      #This library is used to listen for packages
import struct      #This library is used to help with handling binary data
import textwrap    #This library is used to format data packages and put limited data on one line
from getmac import get_mac_address as gma
from datetime import datetime

#Unpack ethernet frame, whenever a packet is detected it's passed in to this function to be unpacked
def ethernet_frame(data):                                            # The reason why we only unpack the first 14 bytes is bc it gives dest, source and type of the Ethernet frame
    dest_mac, src_mac, proto = struct.unpack('! 6s 6s H', data[:14]) #The way packets are stored in computers and the way they travel in networks is different, that's why we convert it from big indien to little indien
    return get_mac_addr(dest_mac), get_mac_addr(src_mac), socket.htons(proto), data[14:] #.hton is used to convert bytes in a readable format


#Return formatted readable MAC address (ex: AA:BB:CC:DD:DD:FF)
def get_mac_addr(bytes_addr):
    bytes_str = map ('{:02x}'.format, bytes_addr) #convert each chunk of the mac address to 2 decimal places
    return ':'. join(bytes_str).upper()           #join pieces with a : in between


#Unpack IPv4 packet which was inside of the ethernet packet (IPv4 packet = data or data[:14])
def ipv4_packet(data):
    version_header_lenght = data[0]
    version = version_header_lenght >> 4
    header_length = (version_header_lenght & 15) * 4                                            #we need the header_length to know where the actual useful data starts
    ttl, proto, src, target = struct.unpack('! 8x B B 2x 4s 4s', data[:20])
    return version, header_length, ttl, proto, ipv4(src), ipv4(target), data[header_length:]    #data[header_length:] is the useful daya being transferred such as passwords and usernames
                                                                                                #proto is the protocol which will return a number and tell us if its TCP, UDP and ICMP
                                                                                                #We need to know what kind of protocol it is to get data packages, we need different methods for each protocol
#Returns formatted readable IPv4 address like 172.54.4
def ipv4(addr):
    return '.'.join(map(str, addr))


##################Now we unpack the package according to it's Protocol (Ethernet Frame type) type like ICMP, UDP or TCP ##########################

#Unpacking for ICMP packets, used in network diagnostics
def icmp_packet(data): #ICMP packets are used by network devices, including routers, to send error messages and operational information indicating success or failure when communicating with another IP address.
    icmp_type, code, checksum = struct.unpack('! B B H', data[:4])
    return icmp_type, code, checksum, data[:4]

#Unpacking for TCP segments
def tcp_segment(data):
    (src_port, dest_port, sequence, acknowledgement, offset_reserved_flags) = struct.unpack('! H H L L H', data[:14])
    offset = (offset_reserved_flags >> 12) * 4       #We do bitwise operations to get individual flag values bc they sit together in one pocket
    flag_urg = (offset_reserved_flags & 32) >> 5
    flag_ack = (offset_reserved_flags & 16) >> 4
    flag_psh = (offset_reserved_flags & 8)  >> 3
    flag_rst = (offset_reserved_flags & 4)  >> 2
    flag_syn = (offset_reserved_flags & 2)  >> 1
    flag_fin =  offset_reserved_flags & 1
    return src_port, dest_port, sequence, acknowledgement, flag_urg, flag_ack, flag_psh, flag_rst, flag_syn, flag_fin, data[offset:]

#Unpacking UDP segments
def udp_segment(data):
    src_port, dest_port, size = struct.unpack('! H H 2x H', data[:8])
    return src_port, dest_port, size, data[8:]



#Just formats multi-line data, useful when we are trying to print the data core, instead of a one long line this converts it to multiple shorter lines
def format_multi_line(prefix, string, size=80):
    size -= len(prefix)
    if isinstance(string, bytes):
        string = ''.join(r'\x{:02x}'.format(byte) for byte in string)
        if size % 2:
            size -= 1
    return '\n'.join([prefix + line for line in textwrap.wrap(string, size)])


def printPacket(packet):
    print("PACKET " + str(packet[0]) + " (" + str(packet[1]) + ") IN PORT " + str(packet[3]) + " (protocol: " + str(packet[2]) + ") WITH SIZE " + str(len(packet[4])))

# This method listen all packets in the user machine and determine:
# - whether there are from or to the machnine
# - the destination IP or sender IP depending of the previous point
# - Which port is targeted by the request
def listen():                                                                       #AF_PACKET is for protocol level packet manipulation
    conn = socket.socket(socket.AF_PACKET, socket.SOCK_RAW, socket.ntohs(3))        #.hton is used to convert bytes in a readable format
    
    file_name = "web/data/cache/request.csv"
    with open(file_name, 'w') as file:          # iniating cache file
        file.write("")
    
    while True:                                                                     #SOCK_RAW allows access to the underlying transport provider.
        raw_data, _ = conn.recvfrom(65536)                                          #we give it biggest buffer size of 65536
        target_mac, src_mac, eth_proto, data = ethernet_frame(raw_data)

        # We are checking if this is an IPv4 packet
        if eth_proto == 8:
            (_, _, _, proto, src, target, data) = ipv4_packet(data)

            packet = ["UNDEFINED", "USER_IP", "PROTOCOL", "PORT", "web/data"]
            # 0- RECEIVED/SENT
            # 1- IP ADDRESS
            # 2- TCP/UDP/OTHER
            # 3- PORT NUMBER
            # 4- DATA

            myMacAddress = gma().upper()


            if myMacAddress == src_mac:
                packet[0] = "SENT"
            elif myMacAddress == target_mac:
                packet[0] = "RECEIVED"

            #6 for TCP -> if our IP Protocol is 6 that means it's a TCP packet, so we unpack accordingly
            if proto == 6: ############????????????????????????????? ipv4.proto
                src_port, dest_port, _, _, _, _, _, _, _, _, data = tcp_segment(data)
                
                packet[2] = "TCP"
                packet[4] = data
                if packet[0] == "RECEIVED":
                    packet[3] = str(dest_port)
                    packet[1] = src
                elif packet[0] == "SENT":
                    packet[1] = target
                    packet[3] = str(src_port)
                

            #17 for UDP -> if our IP Protocol is 17 that means it's an UDP packet, so we unpack accordingly
            elif proto == 17:
                src_port, dest_port, _, data = udp_segment(data)

                packet[2] = "UDP"
                packet[4] = data
                if packet[0] == "RECEIVED":
                    packet[3] = str(dest_port)
                    packet[1] = src
                elif packet[0] == "SENT":
                    packet[1] = target
                    packet[3] = str(src_port)
            if packet[0] != "UNDEFINED" and (packet[2] == "TCP" or packet[2] == "UDP"):
                #
                # We're now handling the packets
                #
                ips_blacklist = read_blocklisted_ip()
                bl_result = check_if_ip_is_blacklisted(packet[1], ips_blacklist)
                if bl_result != None:
                    conn.recv(65536)

                    create_log_file("SEVERE", bl_result[0], "Tried to connect to a forbidden IP (" + str(bl_result[0]) + " - " + str(bl_result[1]).replace("\n", "") + ")")

                    continue

                if packet[0] == "RECEIVED":
                    ports = read_open_ports_from_file()
                    port = packet[3]
                    inPorts = False
                    for p in ports:
                        if str(p) == str(port):
                            inPorts = True
                    if inPorts:
                        write_cache(port, len(packet[4]))
                elif packet[0] == "SENT":
                    target = packet[1]

def read_open_ports_from_file():
    open_ports = []
    try:
        with open('web/data/ports.txt', 'r') as file:
            for line in file:
                port = int(line.strip())  # Convert each line to an integer
                open_ports.append(port)
    except FileNotFoundError:
        print("File not found.")
    return open_ports

def create_log_file(type, destination, message):
    now = datetime.now()

    file_name = "web/data/logs/" + now.strftime("%Y-%m-%d-%H-%M-%S-%f.json")
    with open(file_name, 'w') as file:
        file.write("{\"type\": \"" + str(type) + "\", \"destination\": \"" + str(destination) + "\", \"message\": \"" + str(message) + "\"}")

def write_cache(port, packetSize):
    file_name = "web/data/cache/request.csv"
    with open(file_name, 'a') as file:
        file.write(str(port) + ";" + str(packetSize) + "\n")
    return 

def read_blocklisted_ip():
    ips = []
    try:
        with open('web/data/blacklistedips.txt', 'r') as file:
            for l in file:
                content = l.split("#")
                if len(content) == 2:
                    value = []
                    ip_ = content[0].replace(' ', '')
                    value.append(ip_)
                    value.append(content[1])
                    ips.append(value)
    except FileNotFoundError:
        print("File not found.")
    return ips

def check_if_ip_is_blacklisted(ip, blacklist):
    for addr in blacklist:
        if str(addr[0]) == str(ip):
            return addr
    return None