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

r = http_post(WS + '/login/', {
       "email": email,
       "password": "incorrect password"
       })
ut = UnitTest(r, 'hammering prevention')
ut.expect_code(409)
ut.show()

r = http_post(WS + '/login/', {
       "email": email,
       "password": "incorrect password"
       })
ut = UnitTest(r, 'hammering prevention')
ut.expect_code(409)
ut.show()


# SUCCESSFUL LOGIN, BUT FAILED DUE TO HAMMERING PREVENTION
r = http_post(WS + '/login/', {
       "email": email,
       "password": password
       })
ut = UnitTest(r, 'hammering prevention with correct password')
ut.expect_code(409)
ut.show()

sleep(10)

pk_user_id, token, refresh_token = successfulLogin(email, password)

# DELETE USER
deleteUser2(pk_user_id)

