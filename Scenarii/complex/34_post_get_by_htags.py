#!/usr/bin/env python
# -*- coding: UTF-8

import urllib

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


postSimpleMessage(token3, pk_user_id3, tag1='trobo', tag2='trobo', tag3='3->3')
postSimpleMessage(token3, pk_user_id1, tag1='trobo', tag2='trobo', tag3='3->1')

# USER1 GETS HIS FLOW
r = http_get(WS + '/flow/', token=token1)
# show(r)
ut = UnitTest(r, 'Flow with two posts')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'tag1'], 'trobo')
tag_id = ut.get_body_value_from_path(['posts', 0, 'id1'])
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=2)
ut.show()

r = http_get(WS + '/posts/'+str(pk_user_id1)+'/'+str(tag_id), token=token2)
# show(r,True)
ut = UnitTest(r, 'One post with this tag')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=1)
ut.show()

for i in range(0,7):
    postSimpleMessage(token3, pk_user_id1, tag1='trobo', tag2='trobo')

r = http_get(WS + '/posts/'+str(pk_user_id1)+'/'+str(tag_id), token=token2)
# show(r,True)
ut = UnitTest(r, '5 posts with this tag')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'tag1'], 'trobo')
ts = ut.get_body_value_from_path(['posts', 4, 'creation_ts'])
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=5)
ut.show()
# print ts


r = http_get(WS + '/posts/'+str(pk_user_id1)+'/'+str(tag_id)+'/down/'+urllib.quote(ts), token=token2)
# show(r,True)
ut = UnitTest(r, '3 posts with this tag')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'tag1'], 'trobo')
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=3)
ts = ut.get_body_value_from_path(['posts', 2, 'creation_ts'])
ut.show()
# print ts
# print '/posts/'+str(pk_user_id1)+'/'+str(tag_id)+'/up/'+urllib.quote(ts)


# REMONTER LA LISTE DE MESSAGES
r = http_get(WS + '/posts/'+str(pk_user_id1)+'/'+str(tag_id)+'/up/'+urllib.quote(ts), token=token2)
# show(r,True)
ut = UnitTest(r, '5 posts with this tag after '+str(ts))
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'tag1'], 'trobo')
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=5)
ts = ut.get_body_value_from_path(['posts', 0, 'creation_ts'])
ut.show()


r = http_get(WS + '/posts/'+str(pk_user_id1)+'/'+str(tag_id)+'/up/'+urllib.quote(ts), token=token2)
# show(r,True)
ut = UnitTest(r, '2 posts with this tag after '+str(ts))
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'tag1'], 'trobo')
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=2)
ut.show()



# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
deleteUser(token4)
