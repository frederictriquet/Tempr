#!/usr/bin/env python
# -*- coding: UTF-8

import md5, atexit, urllib

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')
execfile(LIBS + '/sub_tests2.py')

def end():
    # raw_input("Press Enter exit...")
    deleteUser(token1)
    deleteUser(token2)

atexit.register(end)

WS = get_ws_url()
email1 = "a@a.com"
email2 = "b@a.com"
pass1 = get_password()
phone = '+33689824777'
fb_id1 = '107093319706000'
fb_token1 = 'EAAVw7YpTBJQBAGH5XwjuNZACKKzcL9rnINt4mwKxOMIOok0AejIafGT3aNDhBe0W2xyd5xLqKrekcug6aTXKrbUI2M6psVheNr0JswexYxZCu5h5bSOlFEKlKBhLNEDSSdbZB5FihxHCssSH2JiZAwydnR45kANdI9ryJmGUdn0O6IClKorM'
fb_token2 = 'EAAVw7YpTBJQBABPjMQ66ZB1XYh2XiXLbdYZBJyeoUmfPQnvPBX6tg6g3cEbp8sLzZAbB9Bw1j5VeZBLT7V9y4vOQxWzFl0RABOPXCTWqEnbY4DZCGaLLHTJ1CmLxR4SXqU7itStDgkfByd0ZBlgSMbMQB6VLGSx3HPKlOu0ys5pAZDZD'
fb_id2 = '101187593631747'
password = 'p4ssw0rd'


# I1()
# I2()
# T()
# T()
# T()
# T()
# sleep(0.1)
# PC()
# sleep(0.1)
# sql('select * from events order by creation_ts', 'events')
# sleep(0.1)
# FA2()
# sql('select * from events order by creation_ts', 'events')

FBI1()
TFB()
sleep(0.1)
TFB()
FBI2()
sleep(0.1)
sql('select * from events order by creation_ts', 'events')
FA2()
sql('select * from events order by creation_ts', 'events')
# sql('select * from friendship_requests', 'friendship_requests')
# sql('select * from pending_friendship_requests', 'pending_friendship_requests')
# sql('select * from pending_users', 'pending_users')
# sql('select * from users', 'users')
# sql('select * from pending_posts', 'pending_posts')
# FR1()
# FA2()

# (121, ('I2', 'I1', 'FR1', 'FA2', 'T', 'PC'))
# I2()
# I1()
# FR1()
# FA2()
# T()
# PC()

#sleep(0.1)
# sql('select * from friendships', 'friendships')
# sql('select * from friendship_requests', 'friendship_requests')
# sql('select * from friendships', 'friendships after temp')
# sql('select * from friendship_requests', 'friendship_requests after temp')
# sql('select * from pending_users', 'pending_users')
# sql('select * from users', 'users')
# sql('select * from pending_posts', 'pending_posts')
# sql('select * from posts', 'posts')
# sql('select * from pending_friendship_requests')
# 
# sleep(0.1)
# sql('select * from friendship_requests')
# sql('select * from friendships')

#CF()
# sleep(0.5)
