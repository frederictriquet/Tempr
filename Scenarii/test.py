#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = './lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
email1 = "a@a.com"
pass1 = get_password()

pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, rt1 = successfulLogin(email1, pass1)
print(pk_user_id1, token1, rt1)

r = http_post(WS + '/hello/', {}, token=token1)
show(r,True)



# DELETE USERS
deleteUser(token1)

