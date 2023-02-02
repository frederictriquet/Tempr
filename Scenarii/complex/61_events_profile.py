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
pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
pk_user_id2, login2 = createUser(email2, "ccc", "ddd", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, tr1 = successfulLogin(email1, pass1)
pk_user_id2, token2, tr2 = successfulLogin(email2, pass1)

# MAKE FRIENDS
# 1---2

requestFriendship(pk_user_id1, token1, pk_user_id2)
sleep(1)

r = http_get(WS + '/profile/', token=token1)
ut = UnitTest(r, 'no event for user 1')
ut.expect_code(200)
ut.expect_body_value(['has_events'], False)
ut.show()

r = http_get(WS + '/profile/', token=token2)
ut = UnitTest(r, 'some events for user 2')
ut.expect_code(200)
ut.expect_body_value(['has_events'], True)
ut.show()


r = http_get(WS + '/events/', token=token1)
# show(r,True)
r = http_get(WS + '/events/', token=token2)
# show(r,True)


r = http_get(WS + '/profile/', token=token1)
ut = UnitTest(r, 'still no event for user 1')
ut.expect_code(200)
ut.expect_body_value(['has_events'], False)
ut.show()

r = http_get(WS + '/profile/', token=token2)
ut = UnitTest(r, 'no more events for user 2')
ut.expect_code(200)
ut.expect_body_value(['has_events'], False)
ut.show()


acceptFriendship(pk_user_id2, token2, pk_user_id1)
sleep(1)



r = http_get(WS + '/profile/', token=token1)
ut = UnitTest(r, 'some events for user 1')
ut.expect_code(200)
ut.expect_body_value(['has_events'], True)
ut.show()

r = http_get(WS + '/profile/', token=token2)
ut = UnitTest(r, 'no event for user 2')
ut.expect_code(200)
ut.expect_body_value(['has_events'], False)
ut.show()



r = http_get(WS + '/events/', token=token1)
# show(r,True)
r = http_get(WS + '/events/', token=token2)
# show(r,True)



r = http_get(WS + '/profile/', token=token1)
ut = UnitTest(r, 'no more events for user 1')
ut.expect_code(200)
ut.expect_body_value(['has_events'], False)
ut.show()

r = http_get(WS + '/profile/', token=token2)
ut = UnitTest(r, 'no event for user 2')
ut.expect_code(200)
ut.expect_body_value(['has_events'], False)
ut.show()


# DELETE USERS
deleteUser(token1)
deleteUser(token2)


