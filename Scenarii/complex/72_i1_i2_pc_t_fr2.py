#!/usr/bin/env python
# -*- coding: UTF-8

import md5, atexit, urllib

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')
execfile(LIBS + '/sub_tests2.py')

def end():
    # raw_input("Press Enter exit...")
    deleteUser(token1)
    deleteUser(token2)

atexit.register(end)

WS = get_ws_url()
email1 = "a@a.com"
email2 = "b@a.com"
pass1 = get_password()
phone = '+33689824777'


I1()
I2()
PC()
T()
FR2()
#FR1()
CF()

end()
