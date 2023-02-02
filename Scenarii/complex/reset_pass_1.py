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

# CREATE USER
pk_user_id, login = createUser(email, "rogé", "adr", get_password())

r = http_get(WS + '/noauth/lostpass/' + urllib.quote(email))
show(r)

sql('select * from password_resets')
# DELETE USER
# deleteUser(token)
