#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()

pass1 = get_password()
email1 = "a@a.fr"
email2 = "b@a.fr"
email3 = "c@a.fr"
email4 = "d@a.fr"
pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
pk_user_id2, login2 = createUser(email2, "ccc", "ddd", pass1)
pk_user_id3, login3 = createUser(email3, "eee", "fff", pass1)
pk_user_id4, login4 = createUser(email4, "ggg", "hhh", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, tr1 = successfulLogin(email1, pass1)
pk_user_id2, token2, tr2 = successfulLogin(email2, pass1)
pk_user_id3, token3, tr3 = successfulLogin(email3, pass1)
pk_user_id4, token4, tr4 = successfulLogin(email4, pass1)


# MAKE FRIENDS
# 1---2
# |   |
# 3   4
requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)

requestFriendship(pk_user_id1, token1, pk_user_id3)
acceptFriendship(pk_user_id3, token3, pk_user_id1)

requestFriendship(pk_user_id4, token4, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id4)


postSimpleMessage(token1, pk_user_id1, "my first message", tag1='prettySmile', tag2='swag')

checkFlow(pk_user_id1, token1, 1, 'from me')
checkFlow(pk_user_id2, token2, 1, 'from 1')
checkFlow(pk_user_id3, token3, 1, 'from 1')
checkFlowNone(pk_user_id4, token4)

# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from friendships;'")
# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from flows;'")

postSimpleMessage(token1, pk_user_id2, "my second message", tag1='swag', tag2='handsome')
checkFlow(pk_user_id1, token1, 2, 'from me')
checkFlow(pk_user_id2, token2, 2, 'from 1')
checkFlow(pk_user_id3, token3, 2, 'from 1')
checkFlow(pk_user_id4, token4, 1, 'from 1 to 2')

# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
deleteUser(token4)
