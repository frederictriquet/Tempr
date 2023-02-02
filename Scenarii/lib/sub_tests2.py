#!/usr/bin/env python
# -*- coding: UTF-8

def I1():
    global pk_user_id1
    global token1
    pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
    pk_user_id1, token1, rt1 = successfulLogin(email1, pass1)

def I2():
    global pk_user_id2
    global token2
    pk_user_id2, dummy = createUser(email2, "ccc", "ddd", pass1)
    pk_user_id2, token2, rt2 = successfulLogin(email2, pass1)

def FBI1():
    global fb_token1
    global pk_user_id1
    global fb_id1
    global token1
    pk_user_id1, dummy, dummy2, dummy3, fb_id_dummy = createFBUser(fb_token1)
    pk_user_id1, token1 = successfulFBLogin(fb_token1)

def FBI2():
    global fb_token2
    global pk_user_id2
    global fb_id2
    global token2
    pk_user_id2, dummy, dummy2, dummy3, fb_id_dummy = createFBUser(fb_token2)
    pk_user_id2, token2 = successfulFBLogin(fb_token2)


def FR1():
    requestFriendship(pk_user_id1, token1, pk_user_id2)

def FA2():
    acceptFriendship(pk_user_id2, token2, pk_user_id1)

def FR2():
    requestFriendship(0, token2, pk_user_id1)

def FA1():
    acceptFriendship(pk_user_id1, token1, pk_user_id2)

def PC():
    r = http_post(WS + '/profile/infos/', {'field':'phone', 'value':phone}, token=token2)
    r = http_post(WS + '/test/profile/confirm/phone/' + str(pk_user_id2), {}, token=token2)

def FBC():
    r = http_post(WS + '/profile/fb/' + fb_token2, {}, token=token2)
#     show(r, True)
    ut = UnitTest(r, 'Post message to phone number')
    ut.expect_code(200)
    ut.show()
def T():
    # POST MESSAGE VIA PHONE NUMBER
    r = http_post(WS + '/post/phone/', {
           'phone':phone,
           'tags':['a', '', '']
           }, token=token1)
    # show(r)
    ut = UnitTest(r, 'Post message to phone number')
    ut.expect_code(200)
    ut.show()

def TFB():
    global fb_id2
    # POST MESSAGE VIA FB ID
    r = http_post(WS + '/post/fb/', {
           'fb':fb_id2,
           'tags':['a', '', '']
           }, token=token1)
    # show(r)
    ut = UnitTest(r, 'Post message to facebook id')
    ut.expect_code(200)
    ut.show()

def CF():
    # GET FLOW
    r = http_get(WS + '/flow/', token=token1)
    # show(r)
    ut = UnitTest(r, 'User 1 sees 1 post')
    ut.expect_code(200)
    ut.expect_array('body', ut.get_body_value_from_path(['posts']), size=1)
    ut.show()
    
    r = http_get(WS + '/flow/', token=token2)
    # show(r)
    ut = UnitTest(r, 'User 2 sees 1 post')
    ut.expect_code(200)
    ut.expect_array('body', ut.get_body_value_from_path(['posts']), size=1)
    ut.show()
