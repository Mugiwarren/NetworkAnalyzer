from packets_listener import *
from port_checker import *
from bandwith import *
import threading
import asyncio


def main():

    print("Starting main")
    threading.Thread(target=refreshPorts).start()
    threading.Thread(target=listen).start()
    threading.Thread(target=saveBandwith).start()
    return 1

main()