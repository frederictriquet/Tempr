#!/usr/bin/env python
# -*- coding: UTF-8

import md5, urllib

import urllib3
urllib3.disable_warnings()
logging.captureWarnings(True)

# CREATE USER
def createUser(email, firstname, lastname, password):
    r = http_put(WS + '/noauth/user/', {
                                        "email": email,
                                        "firstname": firstname,
                                        "lastname": lastname,
                                        "password": password
    })
#     show(r)
    ut = UnitTest(r, 'create user')
    ut.expect_code(201)
    ut.expect_headers_contain({'content-type': 'application/json'})
    ut.expect_body_has_keys(['created'])
    # ut.expect_body_contains({'created': True})
    ut.expect_body_value(['created'], True)
    ut.expect_body_has_keys(['created', 'pk_user_id', 'login'], True)
    ut.show()
    pk_user_id = ut.get_body_value('pk_user_id')
    login = ut.get_body_value('login')
    return pk_user_id, login


def createFBUser(fb_token):
    r = http_put(WS + '/noauth/user/fb/', {
                                        "token": fb_token
    })
    ut = UnitTest(r, 'create Facebook user')
#     show(r)
    ut.expect_code(201)
    ut.expect_headers_contain({'content-type': 'application/json'})
    ut.expect_body_has_keys(['created'])
    # ut.expect_body_contains({'created': True})
    ut.expect_body_value(['created'], True)
    ut.expect_body_has_keys(['created', 'pk_user_id', 'login', 'firstname', 'lastname', 'facebook_id'], True)
    ut.show()
    pk_user_id = ut.get_body_value('pk_user_id')
    login = ut.get_body_value('login')
    firstname = ut.get_body_value('firstname')
    lastname = ut.get_body_value('lastname')
    facebook_id = ut.get_body_value('facebook_id')
    return pk_user_id, login, firstname, lastname, facebook_id


# SUCCESSFUL LOGIN
def successfulLogin(email, password):
    r = http_post(WS + '/login/', {
                                  "email": email,
                                  "password": password
    })
    ut = UnitTest(r, 'Log in')
    ut.expect_code(200)
    ut.expect_body_has_keys(['logged_in', 'token', 'refresh_token', 'pk_user_id'], True)
    # ut.expect_body_contains({'logged_in':True})
    ut.expect_body_value(['logged_in'], True)
    ut.show()
    # show(r)
    pk_user_id = ut.get_body_value('pk_user_id')
    token = ut.get_body_value('token')
    refresh_token = ut.get_body_value('refresh_token')
    return pk_user_id, token, refresh_token


def successfulFBLogin(fb_token):
    r = http_post(WS + '/login/fb/', {
                                    "token": fb_token
    })
#     show(r)
    ut = UnitTest(r, 'FB Log in')
    ut.expect_code(200)
    ut.expect_body_has_keys(['logged_in', 'token', 'refresh_token', 'pk_user_id'], True)
    # ut.expect_body_contains({'logged_in':True})
    ut.expect_body_value(['logged_in'], True)
    ut.show()
    pk_user_id = ut.get_body_value('pk_user_id')
    token = ut.get_body_value('token')
    refresh_token = ut.get_body_value('refresh_token')
    return pk_user_id, token, refresh_token

# DELETE USER
def deleteUser(token):
    r = http_delete(WS + '/profile/', None, token=token)
    #show(r, True)
    ut = UnitTest(r, 'delete user')
    ut.expect_code(204)
    ut.expect_headers_contain({'content-length': '0'})
    ut.show()

# DELETE USER
def deleteUser2(pk_user_id):
    r = http_delete(WS + '/noauth/user/' + str(pk_user_id), None, login='TemprAdmin', password='AdminPassword')
    ut = UnitTest(r, 'delete user')
    ut.expect_code(204)
    ut.expect_headers_contain({'content-length': '0'})
    ut.show()


# FRIENDSHIP REQUEST
def requestFriendship(pk_user_id1, token1, pk_user_id2, name='Post friendship request'):
    r = http_post(WS + '/friendship/' + str(pk_user_id2), {}, token=token1)
    ut = UnitTest(r, name)
    ut.expect_code(200)
    ut.show()

def acceptFriendship(pk_user_id1, token1, pk_user_id2):
    requestFriendship(pk_user_id1, token1, pk_user_id2, 'Accept friendship request')



def checkFriendshipRequestNone(pk_user_id1, token1):
    r = http_get(WS + '/friendship/', token=token1)
    ut = UnitTest(r, 'No pending friendship request')
    ut.expect_code(200)
    ut.expect_array("body", ut.get_body_value_from_path(), size=0)
    ut.show()

def checkFriendshipRequestWithOneUser(pk_user_id1, token1, pk_user_id2):
    # user_id2 has requested a friendship with us
    r = http_get(WS + '/friendship/', token=token1)
    # show(r)
    ut = UnitTest(r, 'Check friendship requests (1 req)')
    ut.expect_code(200)
    ut.expect_array("body", ut.get_body_value_from_path(), size=1)
    ut.expect_body_has_keys(['fk_user_id1', 'fk_user_id2', 'request_ts'], exactly=True, path=[0])
    ut.expect_body_value([0, 'fk_user_id1'], pk_user_id2)
    ut.show()


def checkFriendshipNone(pk_user_id1, token1):
    r = http_get(WS + '/friends/' + str(pk_user_id1), token=token1)
    # show(r)
    ut = UnitTest(r, 'No friends')
    ut.expect_code(200)
    ut.expect_array("body", ut.get_body_value_from_path(), size=0)
    ut.show()

def checkFriendshipWithOneUser(pk_user_id1, token1, pk_user_id2):
    r = http_get(WS + '/friends/' + str(pk_user_id1), token=token1)
    # show(r)
    ut = UnitTest(r, 'Friend with someone')
    ut.expect_code(200)
    ut.expect_array("body", ut.get_body_value_from_path(), size=1)
    ut.show()

def postRaw(name, token, to_user_id, body=None, media=None, tag1=None, tag2=None, tag3=None, locality=None, countryCode=None, lat=None, lon=None):
    obj = { "to_user_id": to_user_id }
    if body != None: obj["body"] = body
    if media != None: obj["media"] = media
    tags = []
    if tag1 != None: tags.append(tag1)
    if tag2 != None: tags.append(tag2)
    if tag3 != None: tags.append(tag3)
    obj["tags"] = tags
    if locality != None: obj['locality'] = locality
    if countryCode != None: obj['countryCode'] = countryCode
    if lat != None: obj['latitude'] = lat
    if lon != None: obj['longitude'] = lon
    r = http_post(WS + '/post/', obj, token=token)
    return UnitTest(r, name)

def postSimpleMessage(token1, pk_user_id2, body=None, tag1='', tag2='', tag3='', name='Simple message to someone', privacy='public'):
    r = http_post(WS + '/post/', {
           "body": body,
           "to_user_id": pk_user_id2,
           "tags": [tag1, tag2, tag3]
           }, token=token1)
    # show(r)
    ut = UnitTest(r, name)
    ut.expect_code(200)
    ut.show()

def postMessageWithImage(token1, pk_user_id2, filename, body='', tag1='', tag2='', tag3='', name='Post with media'):
    # POST MESSAGE, RETRIEVE uploadUrl FROM S3
    r = http_post(WS + '/post/', {
           'body': body,
           'to_user_id': pk_user_id2,
           'media': 'image/png',
           'tags':[tag1, tag2, tag3]
           }, token=token1)
    # show(r)
    ut = UnitTest(r, 'Post message')
    ut.expect_code(200)
    ut.expect_body_has_keys(['uploadUrl', 'confirmToken'])
    uploadUrl = ut.get_body_value('uploadUrl')
    confirmToken = ut.get_body_value('confirmToken')
    # ut.show()

    r = requests.put(uploadUrl, data=open(filename, 'rb'), headers={'content-type': 'image/png'})
    # show(r)
    ut = UnitTest(r, 'Put file to S3')
    ut.expect_code(200)
    # ut.show()

    r = http_post(WS + '/pending/' + confirmToken, {}, token=token1)

#     ut = UnitTest(r, 'Confirm upload')
#     ut.expect_code(200)
#     ut.expect_body_has_keys(['upload'])
#     ut.expect_body_value(['upload'], 'confirmed')
#     ut.show()
    return r

def checkFlowNone(pk_user_id1, token1):
    r = http_get(WS + '/flow/', token=token1)
    # show(r)
    ut = UnitTest(r, 'Flow is empty')
    ut.expect_code(200)
    ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=0)
    ut.show()

def checkFlow(pk_user_id1, token1, size=0, msg='', start=0):
    r = http_get(WS + '/flow/' + str(start), token=token1)
    #show(r)
    ut = UnitTest(r, str(size) + ' posts available (' + msg + ')')
    ut.expect_code(200)
    ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=size)
    ut.show()
    return ut

def checkFlowDown(pk_user_id1, token1, before_ts=None, size=0, msg=''):
    before = '' if (before_ts is None) else urllib.quote(before_ts)
    r = http_get(WS + '/flow/down/' + before, token=token1)
    # show(r)
    ut = UnitTest(r, str(size) + ' posts available (' + msg + ')')
    ut.expect_code(200)
    ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=size)
    ut.show()
    return ut

def checkFlowUp(pk_user_id1, token1, after_ts, size=0, msg=''):
    r = http_get(WS + '/flow/up/' + urllib.quote(after_ts), token=token1)
    #show(r)
    ut = UnitTest(r, str(size) + ' posts available (' + msg + ')')
    ut.expect_code(200)
    ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=size)
    ut.show()
    return ut

# def checkUserPosts(pk_user_id1, token1, pk_user_id2, size, name, start=0):
#     # print "checking what", pk_user_id1, "sees from", pk_user_id2
#     # print(WS + '/posts/' + str(pk_user_id2))
#     r = http_get(WS + '/posts/' + str(pk_user_id2) + '/' + str(start), token=token1)
#     # c'est pas la bonne API
#     show(r)
#     ut = UnitTest(r, str(size) + ' posts available (' + name + ')')
#     ut.expect_code(20000)
#     ut.expect_array("body", ut.get_body_value_from_path(), size=size)
#     ut.show()

def checkDownloadFile(url, md5sum, name='Get file from S3'):
    r = requests.get(url, stream=True)
    ut = UnitTest(r, name)
    ut.expect_code(200)
    sum = md5.new(r.raw.read()).hexdigest()
    ut.expect_value('MD5 sum', md5sum, sum)
    ut.show()

def postOneComment(token1, pk_post_id, body='the comment'):
    r = http_post(WS + '/comment/'+str(pk_post_id), {'body': body}, token=token1)
    # show(r,True)
    ut = UnitTest(r,'Post comment')
    ut.expect_code(200)
    ut.show()
