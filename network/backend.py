from packets_listener import *
import threading

def main():
    packet_listener_thread = threading.Thread(target=listen)
    packet_listener_thread.start()
    return 1


main()