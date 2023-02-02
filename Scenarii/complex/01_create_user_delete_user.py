#!/usr/bin/env python
# -*- coding: UTF-8
if 'LIBS' not in globals():  LIBS = '../lib'

execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

WS = get_ws_url()

# CREATE USER
pk_user_id, login = createUser("roge@a.com", "rogé", "adr", get_password())

sql("select * from users")

# DELETE USER
deleteUser2(pk_user_id)
