#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()

pass1 = get_password()
email0 = "a0@a.fr"
email1 = "a1@a.fr"
email2 = "a2@a.fr"
email3 = "a3@a.fr"

# SUCCESSFUL LOGIN
pk_user_id0, token0, tr0 = successfulLogin(email0, pass1)
# pk_user_id1, token1, tr1 = successfulLogin(email1, pass1)
# pk_user_id2, token2, tr2 = successfulLogin(email2, pass1)
# pk_user_id3, token3, tr3 = successfulLogin(email3, pass1)


r = http_get(WS + '/profile/' + str(pk_user_id0) + '/alltags/', token=token0)
show(r, True)

r = http_post(WS + '/test/randomizelikes/', {})
show(r)

r = http_get(WS + '/profile/' + str(pk_user_id0) + '/alltags/', token=token0)
show(r, True)
