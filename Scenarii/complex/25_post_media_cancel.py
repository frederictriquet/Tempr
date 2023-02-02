#!/usr/bin/env python
# -*- coding: UTF-8

import md5

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
S3 = ''

email1 = "a@a.fr"
email2 = "b@a.fr"
pass1 = get_password()

pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
pk_user_id2, login2 = createUser(email2, "ccc", "ddd", pass1)
pk_user_id1, token1, tr1 = successfulLogin("a@a.fr", pass1)
pk_user_id2, token2, rt2 = successfulLogin(email2, pass1)

requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)
checkFlowNone(pk_user_id1, token1)
checkFlowNone(pk_user_id2, token2)

body = 'first message with image'
filename = '/mnt/ramdisk/1px.png'
md5sum = md5.new(open(filename).read()).hexdigest()
# print md5sum

# POST MESSAGE, RETRIEVE uploadUrl FROM S3
r = http_post(WS + '/post/', {
       'body': body,
       'to_user_id': pk_user_id1,
       'media': 'image/png',
       'tags':['a', '', '']
       }, token=token1)
# show(r)
ut = UnitTest(r, 'Post message')
ut.expect_code(200)
ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
uploadUrl = ut.get_body_value('uploadUrl')
confirmToken = ut.get_body_value('confirmToken')
ut.show()

# sql('select * from medias')

# EMPTY FLOWS BECAUSE POST IS PENDING
checkFlowNone(pk_user_id1, token1)
checkFlowNone(pk_user_id2, token2)


# USE uploadUrl TO PUT FILE
r = requests.put(uploadUrl, data=open(filename, 'rb'), headers={'content-type': 'image/png'})
# show(r)
ut = UnitTest(r, 'Put file to S3')
ut.expect_code(200)
ut.show()

# CONFIRM THE UPLOAD IS OK
r = http_post(WS + '/pending/' + confirmToken, {}, token=token1)
# show(r)
ut = UnitTest(r, 'Upload confirmed')
ut.expect_code(200)
ut.show()
# sql('select * from posts')
# sql('select * from pending_uploads')
# sql('select * from medias')

ut = UnitTest(r, 'Confirm upload')
ut.expect_code(200)
ut.show()


checkFlow(pk_user_id1, token1, 1, 'from me')
checkFlow(pk_user_id2, token2, 1, 'from my friend')


############## SECOND POST, WITH MEDIA UPLOAD CANCELATION
# POST MESSAGE, RETRIEVE uploadUrl FROM S3
r = http_post(WS + '/post/', {
       'body': body,
       'to_user_id': pk_user_id1,
       'media': 'image/png',
       'tags':['a', '', '']
       }, token=token1)
# show(r)
ut = UnitTest(r, 'Post message')
ut.expect_code(200)
ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
uploadUrl = ut.get_body_value('uploadUrl')
confirmToken = ut.get_body_value('confirmToken')
ut.show()


checkFlow(pk_user_id1, token1, 1, 'from me')
checkFlow(pk_user_id2, token2, 1, 'from my friend')


# CANCEL THE UPLOAD
r = http_delete(WS + '/pending/' + confirmToken, {}, token=token1)
# show(r)
ut = UnitTest(r, 'Upload aborted')
ut.expect_code(200)
ut.show()

checkFlow(pk_user_id1, token1, 1, 'from me')
checkFlow(pk_user_id2, token2, 1, 'from my friend')



# CONFIRM THE UPLOAD IS OK, BUT IT IS TOO LATE
r = http_post(WS + '/pending/' + confirmToken, {}, token=token1)
# show(r)
ut = UnitTest(r, 'No such pending upload')
ut.expect_code(200)
ut.show()

checkFlow(pk_user_id1, token1, 1, 'from me')
checkFlow(pk_user_id2, token2, 1, 'from my friend')


deleteUser(token1)
deleteUser(token2)

# os.system("psql -h 172.16.1.10 -U postgres tempr -c 'select * from htags;'")
