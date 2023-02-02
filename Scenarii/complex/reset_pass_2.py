#!/usr/bin/env python
# -*- coding: UTF-8
if 'LIBS' not in globals():  LIBS = '../lib'

execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

sql('select * from users')

WS = get_ws_url()

login = 'roge-adr'
password = 'Azertyui0p'
# SUCCESSFUL LOGIN
pk_user_id, token, refresh_token = successfulLogin(login, password)

print(pk_user_id, token)
# DELETE USER
# deleteUser(token)
