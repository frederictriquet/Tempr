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


postSimpleMessage(token1, pk_user_id1, "my first message" , 'trobo', 'swag', 'whosTheBoss')
postSimpleMessage(token1, pk_user_id2, "my second message", 'swag', 'whosTheBoss')

# USER1 GETS HIS FLOW
r = http_get(WS + '/flow/', token=token1)
# print('FLOW 1')
# show(r)
ut = UnitTest(r, 'Htags')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'body'], 'my second message')
ut.expect_body_value(['posts', 0, 'tag1'], 'swag')
post_id = ut.get_body_value_from_path(['posts', 0, 'pk_post_id'])
pop1 = ut.get_body_value_from_path(['posts', 0, 'pop1'])
pop2 = ut.get_body_value_from_path(['posts', 0, 'pop2'])

#ut.expect(False, 'TODO')
ut.show()

# USER1 LIKES THE 3RD TAG WHEREAS THERE ARE ONLY TWO TAGS
# TODO

# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
deleteUser(token4)
