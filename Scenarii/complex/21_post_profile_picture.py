#!/usr/bin/env python
# -*- coding: UTF-8

import md5

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
S3 = ''

pass1 = get_password()

pk_user_id1, login1 = createUser('a@a.fr', "aaa", "bbb", pass1)
pk_user_id1, token1, tr1 = successfulLogin('a@a.fr', pass1)

body = 'first message with image'
filename = '/mnt/ramdisk/1px.png'
imgType = 'png'
contentType = 'image/' + imgType
md5sum = md5.new(open(filename).read()).hexdigest()
# print md5sum


# GET uploadUrl TO PUT FILE
r = http_post(WS + '/profile/profile/' + imgType, {}, token=token1)
# show(r)
ut = UnitTest(r, 'Get newpic url')
ut.expect_code(200)
ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
uploadUrl = ut.get_body_value('uploadUrl')
confirmToken = ut.get_body_value('confirmToken')
ut.show()

# sql('select * from pending_uploads')

# USE uploadUrl TO PUT FILE
r = requests.put(uploadUrl, data=open(filename, 'rb'), headers={'content-type': contentType})
# show(r)
ut = UnitTest(r, 'Put file to S3')
ut.expect_code(200)
ut.show()

# CONFIRM THE UPLOAD IS OK
r = http_post(WS + '/pending/' + confirmToken, {}, token=token1)
# show(r)
ut = UnitTest(r, 'Confirm upload')
ut.expect_code(200)
ut.show()

# sql('select * from medias')
# sql('select * from users')
# GET FULL PROFILE
r = http_get(WS + '/profile/', token=token1)
# show(r)


# r = http_delete(WS + '/profile/newpic/', {
#          'confirmToken': confirmToken
#          },
#          token=token1)
# # show(r)
# ut = UnitTest(r, 'Abort upload')
# ut.expect_code(200)
# ut.expect_body_has_keys(['upload'])
# ut.expect_body_value(['upload'], 'aborted')
# ut.show()

# ut = UnitTest(r, 'Check flow content')
# ut.expect_code(200)
# ut.expect_array('body', ut.get_body_value_from_path(), size=1)
# ut.show()
#
# # CHECK CONTENT OF THE AVAILABLE FILE AT 'URL'
# url = ut.get_body_value_from_path([0, 'url'])
# r = checkDownloadFile(url, md5sum)

deleteUser(token1)

# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from htags;'")
