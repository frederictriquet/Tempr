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

body = 'first message with image'
filename = '/mnt/ramdisk/1px.png'
filename_vid = '/mnt/ramdisk/video.mp4'

md5sum = md5.new(open(filename).read()).hexdigest()
md5sum_vid = md5.new(open(filename_vid).read()).hexdigest()

# POST MESSAGE, RETRIEVE uploadUrl FROM S3
r = http_post(WS + '/post/', {
       'body': body,
       'to_user_id': pk_user_id1,
       'media': 'video/mp4',
       'tags':['a', '', '']
       }, token=token1)
show(r)
ut = UnitTest(r, 'Post message')
ut.expect_code(200)
ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
uploadUrl = ut.get_body_value('uploadUrl')
confirmToken = ut.get_body_value('confirmToken')
uploadUrlVid = ut.get_body_value('uploadUrlVid')
confirmTokenVid = ut.get_body_value('confirmTokenVid')
ut.show()

# sql('select * from medias')

# USE uploadUrl TO PUT THUMBNAIL FILE
r = requests.put(uploadUrl, data=open(filename, 'rb'), headers={'content-type': 'image/png'})
# show(r)
ut = UnitTest(r, 'Put thumbnail file to S3')
ut.expect_code(200)
ut.show()

# USE uploadUrlVid TO PUT VIDEO FILE
r = requests.put(uploadUrlVid, data=open(filename_vid, 'rb'), headers={'content-type': 'video/mp4'})
# show(r)
ut = UnitTest(r, 'Put video file to S3')
ut.expect_code(200)
ut.show()

# sql('select * from pending_posts')

# sql('select * from pending_uploads')
# CONFIRM THE THUMBNAIL UPLOAD IS OK
r = http_post(WS + '/pending/' + confirmToken, {}, token=token1)
# show(r)
ut = UnitTest(r, 'Confirm thumbnail upload')
ut.expect_code(200)
ut.show()

# sql('select * from pending_posts')

# CONFIRM THE VIDEO UPLOAD IS OK
r = http_post(WS + '/pending/' + confirmTokenVid, {}, token=token1)
# show(r)
ut = UnitTest(r, 'Confirm video upload')
ut.expect_code(200)
ut.show()


# sql('select * from pending_posts')
# sql('select * from posts')

###### r = http_post(WS + '/test/forcepublish/')

# GET FLOW
r = http_get(WS + '/flow/', token=token1)
# show(r, True)
ut = UnitTest(r, 'Check flow content')
ut.expect_code(200)
ut.expect_array('body', ut.get_body_value_from_path(['posts']), size=1)
ut.show()

# CHECK CONTENT OF THE AVAILABLE FILE AT 'URL'
url = ut.get_body_value_from_path(['posts', 0, 'url'])
r = checkDownloadFile(url, md5sum)

url_vid = ut.get_body_value_from_path(['posts', 0, 'url_vid'])
r = checkDownloadFile(url_vid, md5sum_vid)

# sql('select * from pending_posts')


# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from htags;'")
#end()
