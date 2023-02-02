#!/usr/bin/env python
# -*- coding: UTF-8

import traceback, glob, os, sys
PWD = os.getcwd()
path = os.path.join(PWD, 'complex')
LIBS = os.path.join(PWD, 'lib')

execfile(LIBS + '/utils.py')


os.system("cp -f data/* /mnt/ramdisk")

if len(sys.argv) > 1:
    if '-d' in sys.argv:
        os.system('../Various/sync_db.sh')
        sys.argv.remove('-d')

pattern = '[0-9][0-9]'
if len(sys.argv) == 2:
    pattern = sys.argv[1]

filenames = glob.glob(os.path.join(path, pattern + '*.py'))
filenames.sort()
print get_ws_url()
for filename in filenames:
    print "\033[34m" + filename + "\033[0m"
    try:
        execfile(filename)
    except Exception:
        traceback.print_exc()
