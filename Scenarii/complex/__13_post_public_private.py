#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()

pass1 = "pass"
pass2 = "pass"
pass3 = "pass"
pass4 = "pass"

pk_user_id1, login1 = createUser("a@a.fr", "aaa", "bbb", pass1)
pk_user_id2, login2 = createUser("b@a.fr", "ccc", "ddd", pass2)
pk_user_id3, login3 = createUser("c@a.fr", "eee", "fff", pass3)
pk_user_id4, login4 = createUser("d@a.fr", "ggg", "hhh", pass4)

# SUCCESSFUL LOGIN
pk_user_id1, token1, tr1 = successfulLogin(login1, pass1)
pk_user_id2, token2, tr2 = successfulLogin(login2, pass2)
pk_user_id3, token3, tr3 = successfulLogin(login3, pass3)
pk_user_id4, token4, tr4 = successfulLogin(login4, pass4)


# MAKE FRIENDS
# 1---2
# |
# 3   4
requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)

requestFriendship(pk_user_id1, token1, pk_user_id3)
acceptFriendship(pk_user_id3, token3, pk_user_id1)


# postSimpleMessage(token1, pk_user_id1, "my first message", tag1='swag', tag2='handsome', privacy='private')
postSimpleMessage(token1, pk_user_id1, "my second message", tag1='swag', tag2='handsome')


checkFlow(pk_user_id1, token1, 1, 'from me')
checkFlow(pk_user_id2, token2, 2, "2 sees 1's")
checkFlow(pk_user_id3, token3, 2, "3 sees 1's")
checkFlowNone(pk_user_id4, token4)


# postSimpleMessage(token1, pk_user_id2, "my third message", tag1='swag', tag2='handsome', name="Private post 1->2", privacy='private')
postSimpleMessage(token1, pk_user_id2, "my fourth message", tag1='swag', tag2='handsome', name="Public post 1->2")

checkFlow(pk_user_id1, token1, 4, '1 sees 4 msgs')
checkFlow(pk_user_id2, token2, 4, '2 sees 4 msgs')
checkFlow(pk_user_id3, token3, 4, '3 sees 4 msgs')

# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from posts;'")
checkUserPosts(pk_user_id1, token1, pk_user_id2, 2, "1 sees 2 msgs @2")
# print "4 sees no message (in his flow)"
checkFlowNone(pk_user_id4, token4)
# print "4 sees 2 messages in 1's posts"
checkUserPosts(pk_user_id4, token4, pk_user_id1, 2, "4 sees 2 msgs @1")
# print "4 sees 1 message in 2's posts"
checkUserPosts(pk_user_id4, token4, pk_user_id2, 1, "4 sees 1 msgs @2")
# print "4 sees 0 message in 3's posts"
checkUserPosts(pk_user_id4, token4, pk_user_id3, 0, "4 sees 0 msg @3")

# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from posts;'")

# DELETE USERS
deleteUser(pk_user_id1)
deleteUser(pk_user_id2)
deleteUser(pk_user_id3)
deleteUser(pk_user_id4)

