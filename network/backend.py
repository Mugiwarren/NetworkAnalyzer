from packets_listener import *
from port_checker import *
import threading

def main():
    #packet_listener_thread = threading.Thread(target=listen)
    #packet_listener_thread.start()
    port_thread = threading.Thread(target=start)
    port_thread.start()
    return 1


main()