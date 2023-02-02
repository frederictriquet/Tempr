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
pk_user_id2, login2 = createUser(email2, "ccc", "ddd", pass1)
pk_user_id3, login3 = createUser(email3, "eee", "fff", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, rt1 = successfulLogin(email1, pass1)
pk_user_id2, token2, rt2 = successfulLogin(email2, pass1)
pk_user_id3, token3, rt3 = successfulLogin(email3, pass1)


# MAKE FRIENDS
requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)


checkFlowNone(pk_user_id1, token1)
checkFlowNone(pk_user_id2, token2)

postSimpleMessage(token1, pk_user_id1, name="message 1->1", tag1='aa')

time.sleep(1)

ut = checkFlow(pk_user_id1, token1, 1, 'from/for me')
post_id1 = ut.get_body_value_from_path(['posts', 0, 'pk_post_id'])

checkFlow(pk_user_id2, token2, 1, 'from/to my friend')


# GET SPECIFIC POST
r = http_get(WS + '/post/' + str(post_id1), token=token3)
# show(r, True)
ut = UnitTest(r, "3 can see the 1st post")
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=1)
ut.show()

# TRY TO SHARE POST
r = http_get(WS + '/post/'+str(post_id1)+'/url/', token=token1)
ut = UnitTest(r, 'I can share my post')
ut.expect_code(200)
ut.expect_body_has_keys(['url'], exactly=True)
ut.show()
r = http_get(WS + '/post/'+str(post_id1)+'/url/', token=token2)
ut = UnitTest(r, 'I cannot share his post')
ut.expect_code(403)
ut.show()


r = http_post(WS + '/profile/infos/', {'field':'private', 'value':True}, token=token1)
ut = UnitTest(r, "1 becomes private")
ut.expect_code(200)
ut.show()

r = http_get(WS + '/post/'+str(post_id1)+'/url/', token=token1)
ut = UnitTest(r, 'I cannot share my post, private')
ut.expect_code(403)
ut.show()


r = http_get(WS + '/post/' + str(post_id1), token=token3)
# show(r, True)
ut = UnitTest(r, "3 cannot see the 1st post")
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=0)
ut.show()



postSimpleMessage(token1, pk_user_id2, name="message 1->2", tag1='aa')

time.sleep(1)

checkFlow(pk_user_id1, token1, 2, 'from me')
ut = checkFlow(pk_user_id2, token2, 2, 'from my friend')
post_id2 = ut.get_body_value_from_path(['posts', 0, 'pk_post_id'])


# GET SPECIFIC POST
r = http_get(WS + '/post/' + str(post_id2), token=token3)
# show(r, True)
ut = UnitTest(r, "3 can see the 2nd post")
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=1)
ut.show()

r = http_post(WS + '/profile/infos/', {'field':'private', 'value':True}, token=token2)
ut = UnitTest(r, "2 becomes private")
ut.expect_code(200)
ut.show()

r = http_get(WS + '/post/' + str(post_id2), token=token3)
# show(r, True)
ut = UnitTest(r, "3 cannot see the 2nd post")
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=0)
ut.show()



r = http_post(WS + '/profile/infos/', {'field':'private', 'value':False}, token=token1)
ut = UnitTest(r, "1 becomes public")
ut.expect_code(200)
ut.show()

r = http_get(WS + '/post/' + str(post_id1), token=token3)
# show(r, True)
ut = UnitTest(r, "3 can see the 1st post")
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=1)
ut.show()



r = http_post(WS + '/profile/infos/', {'field':'private', 'value':False}, token=token2)
ut = UnitTest(r, "2 becomes private")
ut.expect_code(200)
ut.show()

r = http_get(WS + '/post/' + str(post_id2), token=token3)
# show(r, True)
ut = UnitTest(r, "3 can see the 2nd post")
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=1)
ut.show()

time.sleep(10)

# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
