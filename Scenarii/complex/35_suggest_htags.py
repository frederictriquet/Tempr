#!/usr/bin/env python
# -*- coding: UTF-8

import urllib

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()

pass1 = get_password()

print BLUE + 'CE TEST NE FONCTIONNE QUE SUR UN USER AVEC DES STATS' + NORMAL
email1 = "a@a.fr"
pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, tr1 = successfulLogin(email1, pass1)

r = http_get(WS + '/tags/1', token=token1)
show(r,True)

# DELETE USERS
deleteUser(token1)
