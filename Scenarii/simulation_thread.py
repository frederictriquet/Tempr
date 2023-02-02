#!/usr/bin/env python
# -*- coding: UTF-8

import time, random, os, sys, threading

PWD = os.getcwd()
path = os.path.join(PWD, 'complex')
LIBS = os.path.join(PWD, 'lib')
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')
execfile(LIBS + '/log_lib.py')

log_init('simu.log', logging.INFO)
start_time = time.time()

settings = {
            'nano': (2, 1, 1),
            'micro': (3, 2, 3),
            'mini': (5, 2, 3),
            'tiny': (5, 2, 10),
            'small': (10, 2, 10),
            'medium': (50, 2, 10),
            't1': (5, 3, 30),
            'preprod': (20, 2, 3)
            }

setting_name = 'mini'
if len(sys.argv) > 1:
    setting_name = sys.argv[1]

NB_USERS, NB_POPULAR, NB_POSTS = settings[setting_name]

delete_users = True
delete_users = False

lastnames = open('data/noms.txt', 'r').read().splitlines()
firstnames = open('data/prenoms.txt', 'r').read().splitlines()
hashtags = open('data/hashtags.txt', 'r').read().splitlines()


def tag():
    return random.choice(hashtags)

def friend_of(id):
    return random.choice(friends[id])

def like(id1, t1):
    print(GREEN + '  USER ' + str(id1) + ': ' + NORMAL),
    sys.stdout.flush()
    start = 0
    nb_likes = 0
    # RETRIEVE FLOW
    r = http_get(WS + '/flow/' + str(start), token=t1)
    o = r.json()
    # print (o)
    o = o['posts']
    while len(o) > 0:
        # print(o)
        for p in o:
            for i in range(1, 4):
                if random.randint(1, 3) > 1:  # about 2 times over 3
                    post_id = p['pk_post_id']
                    dummy = http_post(WS + '/like/' + str(post_id) + '/' + str(i), {}, token=t1)
                    # print('like', post_id, i)
                    nb_likes = nb_likes + 1
 
        start = start + len(o)
        r = http_get(WS + '/flow/' + str(start), token=t1)
        o = r.json()
        o = o['posts']
    print(GREEN + str(nb_likes) + ' LIKES' + NORMAL)


WS = get_ws_url()

users = []

random.seed(1)

password = get_password()
print(BLUE + 'CREATE ' + str(NB_USERS) + ' USERS' + NORMAL)
for i in range(0, NB_USERS):
    e = 'a' + str(i) + '@a.fr'
    f = random.choice(firstnames)
    l = random.choice(lastnames)
    id, dummy = createUser(e, f, l, password)
    id, token, dummy = successfulLogin(e, password)
    users.append((id, token))


popular_users = users[:NB_POPULAR]
other_users = users[NB_POPULAR:]

print(BLUE + 'REQUEST FRIENDSHIP: ' + str(NB_POPULAR) + ' POPULAR USERS' + NORMAL)
for id1, t1 in other_users:
    for id2, dummy in popular_users:
        requestFriendship(id1, t1, id2, str(id1) + ' -> ' + str(id2))




print(BLUE + 'ACCEPT FRIENDSHIPS' + NORMAL)
for id1, t1 in popular_users:
    r = http_get(WS + '/friendship/', token=t1)
    for req in r.json():
        requester_id2 = req['fk_user_id1']
#         print(id1, 'accepts', requester_id2)
        acceptFriendship(id1, t1, requester_id2)


print(BLUE + 'POST ' + str(NB_POSTS) + ' MESSAGES' + NORMAL)
for id1, t1 in other_users:
    for id2, dummy in popular_users:
        print(GREEN + '  ' + str(id1) + ' -> ' + str(id2) + NORMAL)
        for i in range(0, NB_POSTS):
            # print(id1, 'to', id2)
            r = http_post(WS + '/post/', {
                                          "body": 'body',
                                          "to_user_id": id2,
                                          "tags": [tag(), tag(), tag()]
                                          }, token=t1)
 

print(BLUE + 'LIKE TAGS' + NORMAL)
threads = []
for id1, t1 in users:
#    like(id1, t1)
    t = threading.Thread(target = like, args = (id1, t1))
    t.start()
    threads.append(t)
for t in threads:
    t.join()

http_post(WS + '/test/randomizelikes/', {} )

# sql('select count(*) from users;', 'USERS')
# sql('select count(*) from friendships;', 'FRIENDSHIPS')
# sql('select count(*) from posts;', 'POSTS')
# sql('select count(*) from flows;', 'FLOWS')
# sql('select count(*) from htags_likes;', 'LIKES')

# sql('select * from htags_likes;', 'LIKES')

if delete_users:
    for id, dummy in users:
#         print('delete user', id)
        r = http_delete(WS + '/noauth/user/' + str(id), None, login='TemprAdmin', password='AdminPassword')
else:
    print('users not deleted')

print("Total time: %.2fs" % round(time.time() - start_time))

log_info('%s: %d users  %d popular users  %d posts' % (setting_name, NB_USERS, NB_POPULAR, NB_POSTS))
