#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

WS = get_ws_url()
email = "a@a.com"
password = get_password()
# CREATE USER
pk_user_id, login = createUser(email, "rogé", "adr", password)

# SUCCESSFUL LOGIN
pk_user_id, token, refresh_token = successfulLogin(email, password)
print(token, refresh_token)
pk_user_id, token, refresh_token = successfulLogin(email, password)
print(token, refresh_token)

r = http_post(WS + '/login/renew/', {}, token=refresh_token)
show(r)
ut = UnitTest(r, 'Renew')
ut.expect_code(200)
ut.expect_body_has_keys(['token'], True)
new_token = ut.get_body_value('token')
ut.show()


# sql("select * from oauth_tokens")

# SUCCESSFUL LOGOUT
r = http_post(WS + '/logout/', {}, token=new_token)
ut = UnitTest(r, 'Log out')
ut.expect_code(204)
#ut.expect_body_value(['logged_out'], True)
ut.show()


# DELETE USER
deleteUser2(pk_user_id)

