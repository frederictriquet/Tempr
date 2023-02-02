#!/usr/bin/env python
# -*- coding: UTF-8

import md5

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')



def t(firstname, lastname, city, phone, birthdate, private, pn_postaboutyou, pn_friendshiprequest, pn_frienshipacceptance, pn_profileupdated):
    r = http_post(WS + '/profile/infos/', {'field':'firstname', 'value':firstname}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'lastname', 'value':lastname}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'city', 'value':city}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'phone', 'value':phone}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'birthdate', 'value':birthdate}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'private', 'value':private}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'pn_postaboutyou', 'value':pn_postaboutyou}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'pn_friendshiprequest', 'value':pn_friendshiprequest}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'pn_frienshipacceptance', 'value':pn_frienshipacceptance}, token=token1)
    r = http_post(WS + '/profile/infos/', {'field':'pn_profileupdated', 'value':pn_profileupdated}, token=token1)
    # show(r)
    # GET FULL PROFILE
    r = http_get(WS + '/profile/', token=token1)
    # show(r)
    ut = UnitTest(r, 'Profile updated')
    ut.expect_code(200)
    ut.expect_body_value(['firstname'], firstname)
    ut.expect_body_value(['lastname'], lastname)
    ut.expect_body_value(['city'], city)
    ut.expect_body_value(['phone'], phone)
    ut.expect_body_value(['birthdate'], birthdate)
    ut.expect_body_value(['private'], private)
    ut.expect_body_value(['pn_postaboutyou'], pn_postaboutyou)
    ut.expect_body_value(['pn_friendshiprequest'], pn_friendshiprequest)
    ut.expect_body_value(['pn_frienshipacceptance'], pn_frienshipacceptance)
    ut.expect_body_value(['pn_profileupdated'], pn_profileupdated)
    ut.show()




WS = get_ws_url()
S3 = ''

pass1 = get_password()

pk_user_id1, login1 = createUser('a@a.fr', "aaa", "bbb", pass1)
pk_user_id1, token1, tr1 = successfulLogin('a@a.fr', pass1)

t('Igor', 'Stefaniwicz', 'Moscow', '+32132164684', '1975-07-31', True, False, False, False, False)
t('John', 'malkovictjhjksdhfkjh', 'New-York', '+12345678910', '2000-02-28', False, True, True, True, True)

# sql('select * from users')

deleteUser(token1)

