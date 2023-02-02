#!/usr/bin/env python
# -*- coding: UTF-8

import md5, sys

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
S3 = ''

pass1 = get_password()
for i in range(1, 21):
    print i
    pk_user_id1, login1 = createUser('a' + str(i) + '@a.fr', "Fake", "User", pass1)
    pk_user_id1, token1, tr1 = successfulLogin(login1, pass1)
sys.exit()


p_filename = '/mnt/ramdisk/fred.jpg'
p_imgType = 'jpeg'
p_contentType = 'image/' + p_imgType
p_md5sum = md5.new(open(p_filename).read()).hexdigest()

bg_filename = '/mnt/ramdisk/background.jpg'
bg_imgType = 'jpeg'
bg_contentType = 'image/' + bg_imgType
bg_md5sum = md5.new(open(bg_filename).read()).hexdigest()



# GET uploadUrl TO PUT FILE
r = http_post(WS + '/profile/profile/' + p_imgType, {}, token=token1)
#  show(r)
ut = UnitTest(r, 'Get newpic url for profile')
ut.expect_code(200)
ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
uploadUrl = ut.get_body_value('uploadUrl')
confirmToken = ut.get_body_value('confirmToken')
ut.show()

# USE uploadUrl TO PUT FILE
r = requests.put(uploadUrl, data=open(p_filename, 'rb'), headers={'content-type': p_contentType})
show(r)
ut = UnitTest(r, 'Put file to S3')
ut.expect_code(200)
ut.show()

# CONFIRM THE UPLOAD IS OK
r = http_post(WS + '/pending/' + confirmToken, {}, token=token1)

show(r)
ut = UnitTest(r, 'Confirm upload')
ut.expect_code(200)
ut.expect_body_has_keys(['upload'])
ut.expect_body_value(['upload'], 'confirmed')
ut.show()







# GET uploadUrl TO PUT FILE
r = http_post(WS + '/profile/background/' + bg_imgType, {}, token=token1)
#  show(r)
ut = UnitTest(r, 'Get newpic url for background')
ut.expect_code(200)
ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
uploadUrl = ut.get_body_value('uploadUrl')
confirmToken = ut.get_body_value('confirmToken')
ut.show()

# USE uploadUrl TO PUT FILE
r = requests.put(uploadUrl, data=open(bg_filename, 'rb'), headers={'content-type': bg_contentType})
show(r)
ut = UnitTest(r, 'Put file to S3')
ut.expect_code(200)
ut.show()

# CONFIRM THE UPLOAD IS OK
r = http_post(WS + '/pending/' + confirmToken, {}, token=token1)

show(r)
ut = UnitTest(r, 'Confirm upload')
ut.expect_code(200)
ut.expect_body_has_keys(['upload'])
ut.expect_body_value(['upload'], 'confirmed')
ut.show()


# sql('select * from medias')
# sql('select * from users')
# GET FULL PROFILE
r = http_get(WS + '/profile/', token=token1)
show(r)


