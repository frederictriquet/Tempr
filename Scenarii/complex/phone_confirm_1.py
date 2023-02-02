#!/usr/bin/env python
# -*- coding: UTF-8

import urllib

if 'LIBS' not in globals():  LIBS = '../lib'

execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

WS = get_ws_url()

email = "frederic.triquet+tempr@gmail.com"
password = get_password()
phone = '+33689824777'
# CREATE USER
pk_user_id, login = createUser(email, "rogé", "adr", password)
pk_user_id, token, refresh_token = successfulLogin(email, password)
r = http_post(WS + '/profile/infos/', {'field':'phone', 'value':phone}, token=token)

r = http_get(WS + '/profile/confirm/phone/', token=token)
show(r)

# sql('select * from phone_confirmations')
# DELETE USER
# deleteUser(token)




# email = "frederic.triquet+tempr2@gmail.com"
# password = get_password()
# phone = '+33689824777'
# # CREATE USER
# pk_user_id, login = createUser(email, "rogé2", "adr2", password)
# pk_user_id, token, refresh_token = successfulLogin(email, password)
# r = http_post(WS + '/profile/infos/', {'field':'phone', 'value':phone}, token=token)
# 
# r = http_get(WS + '/profile/confirm/phone/', token=token)
# show(r)
