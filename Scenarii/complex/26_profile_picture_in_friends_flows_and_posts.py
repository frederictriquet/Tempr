#!/usr/bin/env python
# -*- coding: UTF-8

import md5

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
S3 = ''

email1 = "a@a.com"
email2 = "b@a.com"
pass1 = get_password()

pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
pk_user_id2, login2 = createUser(email2, "ccc", "ddd", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, rt1 = successfulLogin(email1, pass1)
pk_user_id2, token2, rt2 = successfulLogin(email2, pass1)

# MAKE FRIENDS
requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)


body = 'first message with image'
filename = '/mnt/ramdisk/1px.png'
imgType = 'png'
contentType = 'image/' + imgType
filename2 = '/mnt/ramdisk/1pxblack.jpeg'
imgType2 = 'jpeg'
contentType2 = 'image/' + imgType

md5sum = md5.new(open(filename).read()).hexdigest()
md5sum2 = md5.new(open(filename2).read()).hexdigest()
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


# USER 2
# GET uploadUrl TO PUT FILE
r = http_post(WS + '/profile/profile/' + imgType2, {}, token=token2)
ut = UnitTest(r, 'Get newpic url')
ut.expect_code(200)
ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
uploadUrl = ut.get_body_value('uploadUrl')
confirmToken = ut.get_body_value('confirmToken')
ut.show()
# USE uploadUrl TO PUT FILE
r = requests.put(uploadUrl, data=open(filename2, 'rb'), headers={'content-type': contentType2})
ut = UnitTest(r, 'Put file to S3')
ut.expect_code(200)
ut.show()
# CONFIRM THE UPLOAD IS OK
r = http_post(WS + '/pending/' + confirmToken, {}, token=token2)
# show(r)
ut = UnitTest(r, 'Confirm upload')
ut.expect_code(200)
ut.show()



# GET FULL PROFILE OF USER1
r = http_get(WS + '/profile/' + str(pk_user_id1), token=token2)
# show(r,True)
ut = UnitTest(r, 'Profile of user1 as seen by user2')
ut.expect_body_has_keys(['url_profile'])
ut.expect_code(200)
url1 = ut.get_body_value_from_path(['url_profile'])
url2 = ut.get_body_value_from_path(['friends', 0, 'url_profile'])
ut.show()
# # CHECK CONTENT OF THE AVAILABLE FILE AT 'URL'
checkDownloadFile(url1, md5sum, name='Profile pic of user1')
checkDownloadFile(url2, md5sum2, name='Profile pic of user2')


postSimpleMessage(token2, pk_user_id1, "my first message", tag1='aa')


# GET THE FLOW
r = http_get(WS + '/flow/', token=token1)
# show(r,True)
ut = UnitTest(r, 'user images in the flow')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=1)
from_url_profile = ut.get_body_value_from_path(['posts', 0, 'from_url_profile'])
to_url_profile = ut.get_body_value_from_path(['posts', 0, 'to_url_profile'])
post_id = ut.get_body_value_from_path(['posts', 0, 'pk_post_id'])
ut.show()
# # CHECK CONTENT OF THE AVAILABLE FILE AT 'URL'
checkDownloadFile(to_url_profile, md5sum, name='Profile pic of user1')
checkDownloadFile(from_url_profile, md5sum2, name='Profile pic of user2')


# GET SPECIFIC POST
r = http_get(WS + '/post/' + str(post_id), token=token2)
# TODO COMPLETER TEST
show(r, True)


#
# # CHECK CONTENT OF THE AVAILABLE FILE AT 'URL'
# url = ut.get_body_value_from_path([0, 'url'])
# r = checkDownloadFile(url, md5sum)

deleteUser(token1)
deleteUser(token2)

