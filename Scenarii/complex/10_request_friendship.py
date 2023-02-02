#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
email1 = "a@a.com"
email2 = "b@a.com"
pass1 = get_password()

pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
pk_user_id2, login2 = createUser(email2, "ccc", "ddd", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, rt1 = successfulLogin(email1, pass1)
pk_user_id2, token2, rt2 = successfulLogin(email2, pass1)


# CHECKS FRIENDSHIP REQUESTS -> nothing
checkFriendshipRequestNone(pk_user_id1, token1)
checkFriendshipRequestNone(pk_user_id2, token2)


# GET FRIENDS -> nothing
checkFriendshipNone(pk_user_id1, token1)
checkFriendshipNone(pk_user_id2, token2)



# "1" REQUESTS FRIENDSHIP WITH "2"
requestFriendship(pk_user_id1, token1, pk_user_id2)


# "2" CHECKS FRIENDSHIP REQUESTS -> request from "1"
checkFriendshipRequestWithOneUser(pk_user_id2, token2, pk_user_id1)


# print pk_user_id2, ut.get_body_value_from_path([0, 'fk_user_id2'])
# "2" ACCEPT FRIENDSHIP FROM "1"
acceptFriendship(pk_user_id2, token2, pk_user_id1)

# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from friendship_requests;'")

# "1" GET FRIENDS -> "2"
checkFriendshipWithOneUser(pk_user_id1, token1, pk_user_id2)
# "2" GET FRIENDS -> "1"
checkFriendshipWithOneUser(pk_user_id2, token2, pk_user_id1)


# CHECKS FRIENDSHIP REQUESTS -> nothing
checkFriendshipRequestNone(pk_user_id1, token1)
checkFriendshipRequestNone(pk_user_id2, token2)


sleep(0.1)
# DELETE USERS
deleteUser(token1)
deleteUser(token2)

