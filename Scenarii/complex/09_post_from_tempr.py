#!/usr/bin/env python
# -*- coding: UTF-8

import time
if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
email1 = "a@a.com"
email2 = "b@a.com"
email3 = "c@a.com"
pass1 = get_password()

pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
pk_user_id2, login2 = createUser(email2, "aaa", "bbb", pass1)
pk_user_id3, login3 = createUser(email3, "aaa", "bbb", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, rt1 = successfulLogin(email1, pass1)
pk_user_id2, token2, rt1 = successfulLogin(email2, pass1)
pk_user_id3, token3, rt1 = successfulLogin(email3, pass1)

requestFriendship(pk_user_id1, token1, pk_user_id3)
acceptFriendship(pk_user_id3, token3, pk_user_id1)


time.sleep(1)

sql("select * from flows", ip="172.16.100.30")


checkFlow(pk_user_id1, token1, 1, 'for me')
checkFlow(pk_user_id2, token2, 1, 'for me')
checkFlow(pk_user_id3, token3, 1, 'for me')
# r = http_get(WS + '/flow/', token=token1)
# show(r,True)

requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)

sql("select * from friendships", ip="172.16.100.30")


checkFlow(pk_user_id1, token1, 1, 'for me')
checkFlow(pk_user_id2, token2, 1, 'for me')
checkFlow(pk_user_id3, token3, 1, 'for me')

# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
