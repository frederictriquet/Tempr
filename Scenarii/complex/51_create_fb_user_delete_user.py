#!/usr/bin/env python
# -*- coding: UTF-8
if 'LIBS' not in globals():  LIBS = '../lib'

execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

WS = get_ws_url()

# CREATE USER
fb_token = "EAAVw7YpTBJQBAJF8w5ibJqT0Q4YIL8HZBt59zJIxDHYpHuX9W5GwHCrUHaome8ZC6g6pENC4RtGlngjYYIEPISd61hO1WWNXbFzvrisTmZBrUGWfSAo08FtC1XWAZCyZANdRPy1PVliX0TsyYhVxngCz4zEeFFc4pS5EekWLlpgZDZD"
pk_user_id, login, firstname, lastname, facebook_id = createFBUser(fb_token)
print(pk_user_id, login, firstname, lastname, facebook_id)
# sql("select * from users")

# DELETE USER
deleteUser2(pk_user_id)
