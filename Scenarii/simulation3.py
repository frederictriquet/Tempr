#!/usr/bin/env python
# -*- coding: UTF-8

import time, random, os

PWD = os.getcwd()
path = os.path.join(PWD, 'complex')
LIBS = os.path.join(PWD, 'lib')
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')
execfile(LIBS + '/log_lib.py')

log_init('simu.log', logging.INFO)
start_time = time.time()

hashtags = open('data/hashtags.txt', 'r').read().splitlines()
files = [ '1px.png', 'empty_profile.png' ]


def tag():
    return random.choice(hashtags)

def file():
    return '/mnt/ramdisk/' + random.choice(files)

WS = get_ws_url()
random.seed(1)
password = get_password()

NB_POSTS = 10

print(BLUE + 'POST ' + str(NB_POSTS) + ' MESSAGES' + NORMAL)
id1, token1, rt = successfulLogin('a0@a.fr', password)
for i in range(0, NB_POSTS):
    r = postMessageWithImage(token1, id1, filename=file(), body='', tag1=tag(), tag2=tag())
    show(r)





# sql('select count(*) from users;', 'USERS')
# sql('select count(*) from friendships;', 'FRIENDSHIPS')
# sql('select count(*) from posts;', 'POSTS')
# sql('select count(*) from flows;', 'FLOWS')
# sql('select count(*) from htags_likes;', 'FLOWS')

print("Total time: %.2fs" % round(time.time() - start_time))

