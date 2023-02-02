#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'

execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

# curl -s -S -i -tutf-8 -XPUT http://172.16.1.10/noauth/user/ -d '{ "login": "user@example.com", "firstname": "roger", "lastname": "adr", "password": "mYp4ss"}'

WS = get_ws_url()

# 404
r = http_put(WS + '/noauth/404/', {})
UT = UnitTest(r, '404 not found')
UT.expect_code(404)
UT.show()

# show(r)
