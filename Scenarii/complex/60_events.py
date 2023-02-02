#!/usr/bin/env python
# -*- coding: UTF-8

import urllib

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()

users = []
password = get_password()
NB_USERS = 10
for i in range(0, NB_USERS):
    e = 'z' + str(i) + '@a.fr'
    id, dummy = createUser(e, 'firstname', 'lastname', password)
    id, token, dummy = successfulLogin(e, password)
    users.append((id, token))

id0 = users[0][0]
token0 = users[0][1]

id1 = users[1][0]
token1 = users[1][1]

for u in users[1:]:
    # print(u[0], id0)
    requestFriendship(0, u[1], id0)

r = http_get(WS + '/events/', token=token0)
# show(r, True)
ut = UnitTest(r, '5 events')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=5)
recent_ts = ut.get_body_value_from_path([0,'creation_ts'])
old_ts = ut.get_body_value_from_path([4,'creation_ts'])
ut.show()


r = http_get(WS + '/events/down/' + urllib.quote(old_ts), token=token0)
# show(r, True)
ut = UnitTest(r, '4 older events')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=4)
ut.show()


r = http_get(WS + '/events/up/' + urllib.quote(recent_ts), token=token0)
# show(r, True)
ut = UnitTest(r, '0 more recent event')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=0)
ut.show()

r = http_get(WS + '/events/up/' + urllib.quote(old_ts), token=token0)
# show(r, True)
ut = UnitTest(r, '4 events older than the 5th one')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=4)
ut.show()

acceptFriendship(0, token0, id1)
# sql('select * from friendships')
# sql('select * from friendship_requests')
postSimpleMessage(token1, id0, "bip", tag1='swag')
postSimpleMessage(token1, id0, "bip", tag1='swag')

r = http_get(WS + '/events/up/' + urllib.quote(old_ts), token=token0)
# show(r, True)
ut = UnitTest(r, '5 events older than the 5th one')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=5)
ut.show()

r = http_get(WS + '/events/up/' + urllib.quote(recent_ts), token=token0)
# show(r, True)
ut = UnitTest(r, '2 events more recent than the first one')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=2)
ut.show()


# sql("select * from events")

# DELETE USERS
for u in users:
    deleteUser(u[1])
