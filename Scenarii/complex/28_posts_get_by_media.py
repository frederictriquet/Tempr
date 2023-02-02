#!/usr/bin/env python
# -*- coding: UTF-8

import md5, atexit

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

def end():
    # raw_input("Press Enter exit...")
    deleteUser(token1)

atexit.register(end)
WS = get_ws_url()
S3 = ''

pass1 = get_password()

pk_user_id1, login1 = createUser("a@a.fr", "aaa", "bbb", pass1)
pk_user_id1, token1, tr1 = successfulLogin("a@a.fr", pass1)


filename = '/mnt/ramdisk/1px.png'
md5sum = md5.new(open(filename).read()).hexdigest()
# print md5sum

for i in range(0,20):
    r = http_post(WS + '/post/', {
        'body': 'with media',
        'to_user_id': pk_user_id1,
        'media': 'image/png',
        'tags':['a', '', '']
        }, token=token1)
    # show(r)
    ut = UnitTest(r, 'Post message with media    '+str(i))
    ut.expect_code(200)
    ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
    uploadUrl = ut.get_body_value('uploadUrl')
    confirmToken = ut.get_body_value('confirmToken')
    ut.show()

    # USE uploadUrl TO PUT FILE
    r = requests.put(uploadUrl, data=open(filename, 'rb'), headers={'content-type': 'image/png'})
    # show(r)
    ut = UnitTest(r, 'Put file to S3')
    ut.expect_code(200)
    ut.show()

    # CONFIRM THE UPLOAD IS OK
    r = http_post(WS + '/pending/' + confirmToken, {}, token=token1)
    ut = UnitTest(r, 'Confirm upload')
    ut.expect_code(200)
    ut.show()

    r = http_post(WS + '/post/', {
        'body': 'no media',
        'to_user_id': pk_user_id1,
        'tags':['a', '', '']
        }, token=token1)
    # show(r)
    ut = UnitTest(r, 'Post message without media '+str(i))
    ut.expect_code(200)
    ut.show()




sleep(0.5)

r = http_get(WS + '/posts/'+str(pk_user_id1)+'/media/', token=token1)
# show(r,True)
ut = UnitTest(r, '15 posts with media')
ut.expect_code(200)
ts = ut.get_body_value_from_path([14, 'creation_ts'])
ut.expect_array("body", ut.get_body_value_from_path(), size=15)
ut.show()


r = http_get(WS + '/posts/'+str(pk_user_id1)+'/media/down/'+urllib.quote(ts), token=token1)
# show(r,True)
ut = UnitTest(r, '5 posts with media before '+str(ts))
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=5)
ts = ut.get_body_value_from_path([4, 'creation_ts'])
ut.show()


# REMONTER LA LISTE DE MESSAGES
r = http_get(WS + '/posts/'+str(pk_user_id1)+'/media/up/'+urllib.quote(ts), token=token1)
# show(r,True)
ut = UnitTest(r, '15 posts with media after '+str(ts))
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=15)
ts = ut.get_body_value_from_path([0, 'creation_ts'])
ut.show()


r = http_get(WS + '/posts/'+str(pk_user_id1)+'/media/up/'+urllib.quote(ts), token=token1)
# show(r,True)
ut = UnitTest(r, '4 posts with media after '+str(ts))
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=4)
ut.show()


