#!/usr/bin/env python
# -*- coding: UTF-8

import urllib

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()

pass1 = get_password()
email1 = "a@a.fr"
email2 = "b@a.fr"
email3 = "c@a.fr"
email4 = "d@a.fr"
pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)
pk_user_id2, login2 = createUser(email2, "ccc", "ddd", pass1)
pk_user_id3, login3 = createUser(email3, "eee", "fff", pass1)
pk_user_id4, login4 = createUser(email4, "ggg", "hhh", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, tr1 = successfulLogin(email1, pass1)
pk_user_id2, token2, tr2 = successfulLogin(email2, pass1)
pk_user_id3, token3, tr3 = successfulLogin(email3, pass1)
pk_user_id4, token4, tr4 = successfulLogin(email4, pass1)


# MAKE FRIENDS
# 1---2
# |   |
# 3   4
requestFriendship(pk_user_id1, token1, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id1)

requestFriendship(pk_user_id1, token1, pk_user_id3)
acceptFriendship(pk_user_id3, token3, pk_user_id1)

requestFriendship(pk_user_id4, token4, pk_user_id2)
acceptFriendship(pk_user_id2, token2, pk_user_id4)


postSimpleMessage(token1, pk_user_id2, "message 1->2", tag1='prettySmile', tag2='swag')

r = http_get(WS + '/flow/', token=token1)
#show(r)
ut = UnitTest(r, 'user1, flow')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(['posts']), size=1)
post_id = ut.get_body_value_from_path(['posts',0,'pk_post_id'])
ut.show()

# NO COMMENT YET
r = http_get(WS + '/comments/'+str(post_id), token=token1)
# show(r,True)
ut = UnitTest(r,'empty comment list')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=0)
ut.show()


# POST ONE COMMENT
postOneComment(token1, post_id, body='the first comment')

# ONE COMMENT AVAILABLE
r = http_get(WS + '/comments/'+str(post_id), token=token1)
#show(r,True)
ut = UnitTest(r,'one comment list')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=1)
ut.show()


# POST 11 MORE COMMENTS
postOneComment(token1, post_id, body='one comment')
postOneComment(token1, post_id, body='one comment')
postOneComment(token1, post_id, body='one comment')
postOneComment(token1, post_id, body='one comment')
postOneComment(token1, post_id, body='one comment')

postOneComment(token1, post_id, body='one comment')
postOneComment(token2, post_id, body='one comment')
postOneComment(token3, post_id, body='one comment')
postOneComment(token4, post_id, body='one comment')
postOneComment(token1, post_id, body='one comment')
postOneComment(token2, post_id, body='the most recent comment')


#r = http_get(WS + '/flow/', token=token1)
#show(r)


# 10 COMMENTS AVAILABLE
r = http_get(WS + '/comments/'+str(post_id), token=token1)
ut = UnitTest(r,'10  comments')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=10)
oldest_comment_id = ut.get_body_value_from_path([0,'pk_comment_id'])
last_ts = ut.get_body_value_from_path([9,'creation_ts'])
ut.show()
#show(r,True)

#print oldest_comment_id

# 2 MORE COMMENTS AVAILABLE
r = http_get(WS + '/comments/'+str(post_id)+'/'+urllib.quote(last_ts), token=token1)
ut = UnitTest(r,'2 more comments')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=2)
ut.show()
#show(r,True)



# DELETE THE OLDEST COMMENT
r = http_delete(WS + '/comment/'+str(oldest_comment_id), {}, token=token2)
#show(r, True)
ut = UnitTest(r,'delete 1 comment')
ut.expect_code(200)
ut.show()

# 10 COMMENTS AVAILABLE
r = http_get(WS + '/comments/'+str(post_id), token=token1)
ut = UnitTest(r,'10 comments')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=10)
last_ts = ut.get_body_value_from_path([9,'creation_ts'])
ut.show()

# 1 MORE COMMENT1 AVAILABLE
r = http_get(WS + '/comments/'+str(post_id)+'/'+urllib.quote(last_ts), token=token1)
ut = UnitTest(r,'1 more comment')
ut.expect_code(200)
ut.expect_array("body", ut.get_body_value_from_path(), size=1)
ut.show()

#r = http_get(WS + '/flow/', token=token1)
#show(r)


# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
deleteUser(token4)
