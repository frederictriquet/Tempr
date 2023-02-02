#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

WS = get_ws_url()

email = "roge@a.com"
password = get_password()
# CREATE USER
pk_user_id, login = createUser(email, "Jean-Jacques", "De Suza De la Vega", password)

print login
# FAILED LOGIN
r = http_post(WS + '/login/', {
       "email": email,
       "password": "incorrect password"
       })
ut = UnitTest(r, 'failed login')
ut.expect_code(401)
ut.show()


# SUCCESSFUL LOGIN
pk_user_id, token, refresh_token = successfulLogin(email, password)

# sql("select * from oauth_tokens")

# FAILED LOGOUT
altered_token = token[:-1]
r = http_get(WS + '/logout/', token=altered_token)
# show(r)
ut = UnitTest(r, 'Log out with invalid token')
ut.expect_code(401)
ut.show()

# sql('select * from devices')

# SUCCESSFUL LOGOUT
# r = http_get(WS + '/logout/', token=token)
# ut = UnitTest(r, 'Log out')
# ut.expect_code(200)
# ut.expect_body_value(['logged_out'], True)
# ut.show()

r = http_post(WS + '/logout/', {'iosdevice':'blabla'}, token=token)
ut = UnitTest(r, 'Log out')
ut.expect_code(204)
ut.show()


# DELETE USER
deleteUser2(pk_user_id)

