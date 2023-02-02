#!/usr/bin/env python
# -*- coding: UTF-8
if 'LIBS' not in globals():  LIBS = '../lib'

execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

WS = get_ws_url()

# CREATE USERS
pk_user_id1, login = createUser("a@a.fr", "rogé", "adr", get_password())
pk_user_id2, login = createUser("b@a.fr", "rogé", "adr", get_password())
pk_user_id3, login = createUser("c@a.fr", "rogé", "adr", get_password())

# DELETE USERS
deleteUser2(pk_user_id1)
deleteUser2(pk_user_id2)
deleteUser2(pk_user_id3)
