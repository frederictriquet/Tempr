#!/usr/bin/env python
# -*- coding: UTF-8

import md5, urllib

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
S3 = ''

email1 = 'a@a.fr'
email2 = 'b@a.fr'
email3 = 'c@a.fr'
pass1 = get_password()

firstname = 'Igor'
lastname = 'Stefaniwicz'
city = 'Moscow'
phone1 = '+32132164684'
phone1bis = '+32100000000'
phone2 = '+321987987987987'
phone3 = '+33612345678'
birthdate = '1975-07-31'

pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
pk_user_id1, token1, tr1 = successfulLogin(email1, pass1)

pk_user_id2, login2 = createUser(email2, "aaa", "bbb", pass1)
pk_user_id2, token2, tr2 = successfulLogin(email2, pass1)

pk_user_id3, login3 = createUser(email3, "aaa", "bbb", pass1)
pk_user_id3, token3, tr3 = successfulLogin(email3, pass1)

# SET PHONE NUMBER
r = http_post(WS + '/profile/infos/', {'field':'phone', 'value':phone1}, token=token1)
r = http_post(WS + '/profile/infos/', {'field':'phone', 'value':phone2}, token=token2)
r = http_post(WS + '/profile/infos/', {'field':'phone', 'value':phone3}, token=token3)
# show(r)

sql('select * from users')
print('EMAIL NON CONFIRME DONC PAS POSSIBLE DE LE TROUVER')
# SEARCH BY EMAIL
r = http_get(WS + '/search/email/' + email1, token=token2)
show(r)
ut = UnitTest(r, 'Search by email')
ut.expect_code(200)
ut.expect_body_value([0, 'pk_user_id'], pk_user_id1)
ut.show()


# SEARCH BY PHONE
r = http_get(WS + '/search/phone/' + urllib.quote(phone1), token=token2)
# show(r)
ut = UnitTest(r, 'Search by phone')
ut.expect_code(200)
ut.expect_array('body', ut.get_body_value_from_path(), size=1)
ut.expect_body_value([0, 'pk_user_id'], pk_user_id1)
ut.show()




# CHANGE PHONE NUMBER
r = http_post(WS + '/profile/infos/', {'field':'phone', 'value':phone1bis}, token=token1)
# show(r)

# SEARCH BY PHONE: USING PREVIOUS VALUE -> NO RESULT
r = http_get(WS + '/search/phone/' + urllib.quote(phone1), token=token2)
# show(r)
ut = UnitTest(r, 'Search by old phone')
ut.expect_code(200)
ut.expect_array('body', ut.get_body_value_from_path(), size=0)
ut.show()


# SEARCH BY PHONE
r = http_get(WS + '/search/phone/' + urllib.quote(phone1bis), token=token2)
# show(r)
ut = UnitTest(r, 'Search by old phone')
ut.expect_code(200)
ut.expect_array('body', ut.get_body_value_from_path(), size=1)
ut.expect_body_value([0, 'pk_user_id'], pk_user_id1)
ut.show()


# SEARCH MULTIPLE PHONE NUMBERS
phoneNumbers = [ phone1bis, phone2 ]
r = http_post(WS + '/search/phones/', phoneNumbers, token=token2)
# show(r)
ut = UnitTest(r, 'Search multiple phone numbers')
ut.expect_code(200)
ut.expect_array('body', ut.get_body_value_from_path(), size=2)
ut.show()


deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
