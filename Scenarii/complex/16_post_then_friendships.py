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

# EVERYONE POSTS A MESSAGE ABOUT SELF
postSimpleMessage(token1, pk_user_id1, 'message', name='1->1', tag1='prettySmile', tag2='swag')
postSimpleMessage(token2, pk_user_id2, 'message', name='2->2', tag1='swag', tag2='handsome')
postSimpleMessage(token3, pk_user_id3, 'message', name='3->3', tag1='prettySmile', tag2='swag')
postSimpleMessage(token4, pk_user_id4, 'message', name='4->4', tag1='swag', tag2='handsome')

# MAKE FRIENDS
# 1---2
requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)


f1 = checkFlow(pk_user_id1, token1, 2, '1 has 2 posts')
checkFlow(pk_user_id2, token2, 2, '2 has 2 posts')
checkFlow(pk_user_id3, token3, 1, '3 has 1 post')
checkFlow(pk_user_id4, token4, 1, '4 has 1 post')

postSimpleMessage(token1, pk_user_id2, 'message', name='1->2', tag1='prettySmile', tag2='swag')
postSimpleMessage(token2, pk_user_id1, 'message', name='2->1', tag1='swag', tag2='handsome')

f2 = checkFlow(pk_user_id1, token1, 4, '1 has 4 posts')
checkFlow(pk_user_id2, token2, 4, '2 has 4 posts')
checkFlow(pk_user_id3, token3, 1, '3 has 1 post')
checkFlow(pk_user_id4, token4, 1, '4 has 1 post')


# MAKE FRIENDS
# 1---2
# |
# 3
requestFriendship(pk_user_id1, token1, pk_user_id3)
acceptFriendship(pk_user_id3, token3, pk_user_id1)

checkFlow(pk_user_id1, token1, 5, '1 has 5 posts')
checkFlow(pk_user_id2, token2, 4, '2 has 4 posts')
checkFlow(pk_user_id3, token3, 4, '3 has 4 posts')
checkFlow(pk_user_id4, token4, 1, '4 has 1 post')



# MAKE FRIENDS
# 1--2
# |_/
# 3
requestFriendship(pk_user_id2, token2, pk_user_id3)
acceptFriendship(pk_user_id3, token3, pk_user_id2)

checkFlow(pk_user_id1, token1, 5, '1 has 5 posts')
checkFlow(pk_user_id2, token2, 5, '2 has 5 posts')
checkFlow(pk_user_id3, token3, 5, '3 has 5 posts')
checkFlow(pk_user_id4, token4, 1, '4 has 1 post')



# POST DELETIONS
post_id1 = f1.get_body_value_from_path(['posts',1,'pk_post_id'])
# print post_id1
post_id2 = f2.get_body_value_from_path(['posts',0,'pk_post_id'])
# print post_id2

# print (pk_user_id1,pk_user_id2,pk_user_id3,pk_user_id4)
# show(f2.r, True)

# UNAUTHORIZED DELETE
r = http_delete(WS + '/post/' + str(post_id1), {}, token=token4)
ut = UnitTest(r, 'Unauthorized post delete')
ut.expect_code(403)
ut.show()

# DELETE A POST I MADE
r = http_delete(WS + '/post/' + str(post_id1), {}, token=token1)
#show(r, True)
ut = UnitTest(r, 'Delete a post I made')
ut.expect_code(200)
ut.show()

# DELETE A POST SOMEONE MADE FOR ME
r = http_delete(WS + '/post/' + str(post_id2), {}, token=token1)
#show(r, True)
ut = UnitTest(r, 'Delete a post from someone')
ut.expect_code(200)
ut.show()

# CHECK FLOWS
checkFlow(pk_user_id1, token1, 3, '1 has 3 posts')
checkFlow(pk_user_id2, token2, 3, '2 has 3 posts')
checkFlow(pk_user_id3, token3, 3, '3 has 3 posts')
checkFlow(pk_user_id4, token4, 1, '4 has 1 post')

# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
deleteUser(token4)
