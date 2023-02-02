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
filename = '/mnt/ramdisk/1px.png'

create = True
destroy = True
if create:
    pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
    pk_user_id2, login2 = createUser(email2, "ccc", "ddd", pass1)
    pk_user_id3, login3 = createUser(email3, "eee", "fff", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, tr1 = successfulLogin(email1, pass1)
pk_user_id2, token2, tr2 = successfulLogin(email2, pass1)
pk_user_id3, token3, tr3 = successfulLogin(email3, pass1)


# MAKE FRIENDS
# 1---2
# |
# 3
if create:
    requestFriendship(pk_user_id1, token1, pk_user_id2)
    acceptFriendship(pk_user_id2, token2, pk_user_id1)

    requestFriendship(pk_user_id1, token1, pk_user_id3)
    acceptFriendship(pk_user_id3, token3, pk_user_id1)


r = http_post(WS + '/profile/infos/', {'field':'private', 'value':False}, token=token2)
r = http_post(WS + '/profile/infos/', {'field':'private', 'value':True}, token=token1)

# user2 posts for self
postMessageWithImage(token2, pk_user_id2, filename)

# user1 posts for user2
postMessageWithImage(token1, pk_user_id2, filename)

# user2 posts for user1
postMessageWithImage(token2, pk_user_id1, filename)

r = http_get(WS + '/profile/' + str(pk_user_id2), token=token3)
ut = UnitTest(r, 'Not friends, but public, should be full')
ut.expect_code(200)
ut.expect_body_value(['is_full'], True)
ut.show()
show(r, True)


r = http_post(WS + '/profile/infos/', {'field':'private', 'value':False}, token=token1)
r = http_get(WS + '/profile/' + str(pk_user_id2), token=token3)
show(r, True)


# DELETE USERS
if destroy:
    deleteUser(token1)
    deleteUser(token2)
    deleteUser(token3)
