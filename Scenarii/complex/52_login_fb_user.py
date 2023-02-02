#!/usr/bin/env python
# -*- coding: UTF-8
if 'LIBS' not in globals():  LIBS = '../lib'

execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

WS = get_ws_url()

fb_token = "EAAVw7YpTBJQBAJF8w5ibJqT0Q4YIL8HZBt59zJIxDHYpHuX9W5GwHCrUHaome8ZC6g6pENC4RtGlngjYYIEPISd61hO1WWNXbFzvrisTmZBrUGWfSAo08FtC1XWAZCyZANdRPy1PVliX0TsyYhVxngCz4zEeFFc4pS5EekWLlpgZDZD"
# CREATE USER
pk_user_id, login, firstname, lastname, facebook_id = createFBUser(fb_token)
print(pk_user_id, login, firstname, lastname, facebook_id)
# sql("select * from users")


pk_user_id, token, refresh_token = successfulFBLogin(fb_token)
print(pk_user_id, token, refresh_token)

pk_user_id, token, refresh_token = successfulFBLogin(fb_token)
print(pk_user_id, token, refresh_token)

r = http_post(WS + '/login/renew/', {}, token=refresh_token)
#show(r)
ut = UnitTest(r, 'Renew')
ut.expect_code(200)
ut.expect_body_has_keys(['token'], True)
new_token = ut.get_body_value('token')
ut.show()
print(BLUE+new_token+NORMAL)

r = http_post(WS + '/login/renew/', {}, token=refresh_token)
#show(r)
ut = UnitTest(r, 'Renew')
ut.expect_code(200)
ut.expect_body_has_keys(['token'], True)
new_token = ut.get_body_value('token')
ut.show()
print(BLUE+new_token+NORMAL)

token = new_token

# DELETE USER
deleteUser(token)
