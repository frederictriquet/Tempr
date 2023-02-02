#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()

pass1 = get_password()
email1 = "a@a.fr"
pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, tr1 = successfulLogin(email1, pass1)

ut = postRaw('post tag none', token1, pk_user_id1, body='dummy', media=None, tag1=None, tag2=None, tag3=None)
ut.expect_code(400)
ut.expect_body_value(['message'], 'incorrect number of tags')
ut.show()

ut = postRaw('post incorrect media type', token1, pk_user_id1, body=None, media='None', tag1=None, tag2=None, tag3=None)
ut.expect_code(400)
ut.expect_body_value(['message'], 'incorrect media type')
ut.show()

ut = postRaw('post incorrect tag', token1, pk_user_id1, body='None', media=None, tag1='no space allowed ', tag2=None, tag3=None)
ut.expect_code(400)
ut.expect_body_value(['message'], 'incorrect tag')
ut.show()

ut = postRaw('post OK', token1, pk_user_id1, body='None', media=None, tag1=None, tag2='None', tag3=None)
ut.expect_code(200)
ut.show()

# show(ut.r)

checkFlow(pk_user_id1, token1, size=1, msg='', start=0)

# DELETE USERS
deleteUser(token1)


