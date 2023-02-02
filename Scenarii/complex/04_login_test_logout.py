#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

import os

WS = get_ws_url()
email = "a@a.com"
pass1 = get_password()
pk_user_id1, login1 = createUser(email, "aaa", "bbb", pass1)
# SUCCESSFUL LOGIN
pk_user_id, token, refresh_token = successfulLogin(email, pass1)

# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from oauth_tokens;'")

# GET TESTAUTH
r = http_get(WS + '/testauth/', token=token)
ut = UnitTest(r, 'Get something (requires user logged in)')
ut.expect_code(200)
ut.show()
# show(r)

# SUCCESSFUL LOGOUT
r = http_post(WS + '/logout/', {}, token=token)
ut = UnitTest(r, 'Log out')
ut.expect_code(204)
ut.show()


# en fait non, parce qu'on peut être loggé sur plusieurs devices (et on utilise le même token)
# # FAILED GET TESTAUTH
# r = http_get(WS + '/testauth/', token=token)
# # show(r)
# ut = UnitTest(r, 'Get something while logged out')
# ut.expect_code(401)
# ut.show()



deleteUser(token)
