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


checkFlowNone(pk_user_id1, token1)
checkFlowNone(pk_user_id2, token2)

ut = postRaw('post simple message to 2 -> forbidden', token1, pk_user_id2, body='body', tag1='a')
ut.expect_code(403)
ut.show()

checkFlowNone(pk_user_id1, token1)
checkFlowNone(pk_user_id2, token2)

# DELETE USERS
deleteUser(token1)
deleteUser(token2)

