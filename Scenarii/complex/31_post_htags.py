#!/usr/bin/env python
# -*- coding: UTF-8

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


postSimpleMessage(token1, pk_user_id1, "my first message" , 'trobo', 'swag', 'whosTheBoss')

# checkFlow(pk_user_id1, token1, 1, 'from me')
# checkFlow(pk_user_id2, token2, 1, 'from 1')
# checkFlow(pk_user_id3, token3, 1, 'from 1')
# checkFlowNone(pk_user_id4, token4)


postSimpleMessage(token1, pk_user_id2, "my second message", 'swag', 'whosTheBoss')
# checkFlow(pk_user_id1, token1, 2, 'from me')
# checkFlow(pk_user_id2, token2, 2, 'from 1')
# checkFlow(pk_user_id3, token3, 2, 'from 1')
# checkFlow(pk_user_id4, token4, 1, 'from 1 to 2')

req = "\
    SELECT p.*, ph.ck_seq_id, ph.pop, h.tag \
        FROM posts p \
            LEFT JOIN posts_htags ph ON ph.fk_post_id = p.pk_post_id \
            LEFT JOIN htags h ON h.pk_htag_id = ph.fk_htag_id\
"

req = "\
    SELECT p.*, \
        string_agg(CASE WHEN ph.ck_seq_id=1 THEN h.tag END,'') AS tag1,\
        string_agg(CASE WHEN ph.ck_seq_id=2 THEN h.tag END,'') AS tag2,\
        string_agg(CASE WHEN ph.ck_seq_id=3 THEN h.tag END,'') AS tag3,\
        SUM(CASE WHEN ph.ck_seq_id=1 THEN ph.pop END) AS pop1,\
        SUM(CASE WHEN ph.ck_seq_id=2 THEN ph.pop END) AS pop2,\
        SUM(CASE WHEN ph.ck_seq_id=3 THEN ph.pop END) AS pop3\
        FROM posts p \
            LEFT JOIN posts_htags ph ON ph.fk_post_id = p.pk_post_id \
            LEFT JOIN htags h ON h.pk_htag_id = ph.fk_htag_id\
        GROUP BY p.pk_post_id\
"

# os.system("PAGER=cat psql -h 172.16.1.10 -U postgres tempr -c '" + req.replace("'", "'\\''") + "'")
# os.system("PAGER=cat psql -h 172.16.1.10 -U postgres tempr -c 'select * from flow_posts_get(" + str(pk_user_id1) + ");'")
# os.system("PAGER=cat psql -h 172.16.1.10 -U postgres tempr -c 'select * from flow_posts_get(" + str(pk_user_id2) + ");'")
# os.system("PAGER=cat psql -h 172.16.1.10 -U postgres tempr -c 'select * from flow_posts_get(" + str(pk_user_id3) + ");'")
# os.system("PAGER=cat psql -h 172.16.1.10 -U postgres tempr -c 'select * from flow_posts_get(" + str(pk_user_id4) + ");'")

# checkFlow(pk_user_id1, token1, 2, 'from me')
# checkFlow(pk_user_id2, token2, 2, 'from my friend')


# USER1 GETS HIS FLOW
r = http_get(WS + '/flow/', token=token1)
# print('FLOW 1')
# show(r)
ut = UnitTest(r, 'Htags')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'body'], 'my second message')
ut.expect_body_value(['posts', 0, 'tag1'], 'swag')
post_id = ut.get_body_value_from_path(['posts', 0, 'pk_post_id'])
pop1 = ut.get_body_value_from_path(['posts', 0, 'pop1'])
pop2 = ut.get_body_value_from_path(['posts', 0, 'pop2'])
ut.show()

# USER1 LIKES TWO TAGS OF THE FIRST POST
r = http_post(WS + '/like/' + str(post_id) + '/1', {}, token=token1)
ut = UnitTest(r, 'Like tag')
ut.expect_code(200)
ut.show()
r = http_post(WS + '/like/' + str(post_id) + '/2', {}, token=token1)
ut = UnitTest(r, 'Like tag')
ut.expect_code(200)
ut.show()



# USER2 GETS HIS FLOW
r = http_get(WS + '/flow/', token=token2)
# print('FLOW 2')
# show(r)
ut = UnitTest(r, 'Htags 2')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'body'], 'my second message')
ut.expect_body_value(['posts', 0, 'tag1'], 'swag')
post_id = ut.get_body_value_from_path(['posts', 0, 'pk_post_id'])

# USER2 LIKES ONE TAG OF THE FIRST POST
r = http_post(WS + '/like/' + str(post_id) + '/2', {}, token=token2)
ut = UnitTest(r, 'Like tag')
ut.expect_code(200)
ut.show()





r = http_get(WS + '/flow/', token=token1)
# print('FLOW 1 AFTER ')
# show(r)
ut = UnitTest(r, 'Likes count')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'pop1'], pop1 + 1)
ut.expect_body_value(['posts', 0, 'pop2'], pop2 + 2)
ut.show()




# USER2 LIKES THE SAME TAG TWICE
r = http_post(WS + '/like/' + str(post_id) + '/2', {}, token=token2)
ut = UnitTest(r, 'Like tag')
ut.expect_code(200)
ut.show()
# show(r)


r = http_get(WS + '/flow/', token=token1)
# print('FLOW 1 AFTER ')
# show(r)
ut = UnitTest(r, 'Likes count did not change')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'pop1'], pop1 + 1)
ut.expect_body_value(['posts', 0, 'pop2'], pop2 + 2)
ut.show()






# USER2 UNLIKES THE TAG
r = http_post(WS + '/unlike/' + str(post_id) + '/2', {}, token=token2)
ut = UnitTest(r, 'Unlike tag')
ut.expect_code(200)
ut.show()


r = http_get(WS + '/flow/', token=token1)
# print('FLOW 1 AFTER ')
# show(r)
ut = UnitTest(r, 'Likes count has decremented')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'pop1'], pop1 + 1)
ut.expect_body_value(['posts', 0, 'pop2'], pop2 + 1)
ut.show()


# USER2 UNLIKES THE TAG TWICE
r = http_post(WS + '/unlike/' + str(post_id) + '/2', {}, token=token2)
ut = UnitTest(r, 'Likes count did not change')
ut.expect_code(200)
ut.show()
# show(r)

r = http_get(WS + '/flow/', token=token1)
# print('FLOW 1 AFTER ')
# show(r)
ut = UnitTest(r, 'Likes count has not decremented twice')
ut.expect_code(200)
ut.expect_body_value(['posts', 0, 'pop1'], pop1 + 1)
ut.expect_body_value(['posts', 0, 'pop2'], pop2 + 1)
ut.show()


# DELETE USERS
deleteUser(token1)
deleteUser(token2)
deleteUser(token3)
deleteUser(token4)
