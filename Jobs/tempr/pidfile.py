#!/usr/bin/env python
# -*- coding: UTF-8

import os
import sys, psutil

def init(pidfile, argv0):
    pid = str(os.getpid())
    try:
        existing_pid = file(pidfile, 'r').read()
        p = psutil.Process(int(existing_pid))
        # print(p.name(), p.cmdline())
        if p.cmdline()[1] == argv0:
            print("already up")
            sys.exit(-2)
        # print(str(p.as_dict()))
    except Exception:
        # pidfile does not exist
        # incorrect pid
        pass
    print("ok",pidfile,pid)
    file(pidfile, 'w').write(pid)


def clean(pidfile):
    os.unlink(pidfile)
