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
# |
# 3   4
requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)

requestFriendship(pk_user_id1, token1, pk_user_id3)
acceptFriendship(pk_user_id3, token3, pk_user_id1)

for n in range(1, 21):
    postSimpleMessage(token1, pk_user_id2, str(n), tag1='swag', tag2='handsome', privacy='public')
    postSimpleMessage(token2, pk_user_id2, str(n), tag1='swag', tag2='handsome', privacy='public')

# CHECK IF THERE ARE 40 MESSAGES AVAILABLE, USING "GET /flow/down/ts" API, 8 TIMES 5 POSTS
ut = checkFlowDown(pk_user_id1, token1, size = 5, msg = '1 sees 5 posts')
ts1 = ut.get_body_value_from_path(['posts', 4, 'creation_ts'])
ut = checkFlowDown(pk_user_id2, token2, size = 5, msg = '2 sees 5 posts')
ts2 = ut.get_body_value_from_path(['posts', 4, 'creation_ts'])

for n in range(1, 8):
    ut = checkFlowDown(pk_user_id1, token1, before_ts = ts1, size = 5, msg = '1 sees 5 posts before '+str(ts1))
    ts1 = ut.get_body_value_from_path(['posts', 4, 'creation_ts'])
    ut = checkFlowDown(pk_user_id2, token2, before_ts = ts2, size = 5, msg = '2 sees 5 posts before '+str(ts2))
    ts2 = ut.get_body_value_from_path(['posts', 4, 'creation_ts'])

checkFlowDown(pk_user_id1, token1, before_ts = ts1, size = 0, msg = '1 sees no more post before '+str(ts1))
checkFlowDown(pk_user_id2, token2, before_ts = ts2, size = 0, msg = '2 sees no more post before '+str(ts2))




# CHECK IF THERE ARE 39 MESSAGES MORE RECENT THAN THE LAST ONES WE GOT, USING "GET /flow/up/ts" API
for n in range(0, 7):
    ut = checkFlowUp(pk_user_id1, token1, after_ts = ts1, size = 5, msg = '1 sees 5 posts after '+str(ts1))
    ts1 = ut.get_body_value_from_path(['posts', 0, 'creation_ts'])
    ut = checkFlowUp(pk_user_id2, token2, after_ts = ts2, size = 5, msg = '2 sees 5 posts after '+str(ts2))
    ts2 = ut.get_body_value_from_path(['posts', 0, 'creation_ts'])

checkFlowUp(pk_user_id1, token1, after_ts = ts1, size = 4, msg = '1 sees 4 posts after '+str(ts1))
checkFlowUp(pk_user_id2, token2, after_ts = ts2, size = 4, msg = '2 sees 4 posts after '+str(ts2))




# CHECK IF THERE ARE 40 MESSAGES AVAILABLE, 8 TIMES 5 POSTS
for n in range(0, 8):
    checkFlow(pk_user_id1, token1, 5, '1 sees 5 posts at ' + str(n * 5), start=n * 5)
    checkFlow(pk_user_id2, token2, 5, '2 sees 5 posts at ' + str(n * 5), start=n * 5)
n = 8
checkFlow(pk_user_id1, token1, 0, '1 sees 0 post left at ' + str(n * 5), start=n * 5)
checkFlow(pk_user_id2, token2, 0, '2 sees 0 post left at ' + str(n * 5), start=n * 5)

# checkFlow(pk_user_id3, token3, 5, '3 sees 20 posts')
checkFlowNone(pk_user_id4, token4)


# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
deleteUser(token4)

