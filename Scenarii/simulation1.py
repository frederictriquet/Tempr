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

settings = {
            'nano': (2, 1, 1),
            'micro': (3, 2, 3),
            'mini': (5, 2, 3),
            'tiny': (5, 2, 10),
            'small': (10, 5, 10),
            'medium': (50, 10, 10),
            't1': (5, 3, 30),
            'preprod': (30, 5, 3)
            }

setting_name = 'mini'
if len(sys.argv) > 1:
    setting_name = sys.argv[1]

NB_USERS, NB_FRIENDSHIPS, NB_POSTS = settings[setting_name]

delete_users = True
delete_users = False

lastnames = open('data/noms.txt', 'r').read().splitlines()
firstnames = open('data/prenoms.txt', 'r').read().splitlines()
hashtags = open('data/hashtags.txt', 'r').read().splitlines()

friends = {}


def tag():
    return random.choice(hashtags)

def friend_of(id):
    return random.choice(friends[id])

WS = get_ws_url()

users = []

random.seed(1)

password = get_password()
print(BLUE + 'CREATE ' + str(NB_USERS) + ' USERS' + NORMAL)
for i in range(0, NB_USERS):
    e = 'a' + str(i) + '@a.fr'
    f = random.choice(firstnames)
    l = random.choice(lastnames)
    id, login = createUser(e, f, l, password)
    users.append((id, e))



print(BLUE + 'REQUEST ' + str(NB_FRIENDSHIPS) + ' FRIENDSHIPS' + NORMAL)
for id1, e1 in users:
    dummy, token1, rt = successfulLogin(e1, password)
    for i in range(0, NB_FRIENDSHIPS):
        id2 = id1
        while id2 == id1:
            id2, dummy = random.choice(users)
        # print(id1, 'asks', id2)
        requestFriendship(id1, token1, id2, str(id1) + ' -> ' + str(id2))

# sql('select * from friendship_requests order by fk_user_id1;', 'FRIENDSHIP REQUESTS')





print(BLUE + 'ACCEPT FRIENDSHIPS' + NORMAL)
for id1, e1 in users:
    dummy, token1, rt = successfulLogin(e1, password)
    r = http_get(WS + '/friendship/', token=token1)
    # print(r)
    if r.status_code == 401:
        sql('select * from oauth_tokens order by fk_user_id')
    for req in r.json():
        requester_id2 = req['fk_user_id1']
        # print(id1, 'accepts', requester_id2)
        acceptFriendship(id1, token1, requester_id2)

# sql('select * from friendships order by fk_user_id1;', 'FRIENDSHIPS')




print(BLUE + 'RETRIEVE FRIENDS' + NORMAL)
for id1, e1 in users:
    dummy, token1, rt = successfulLogin(e1, password)
    r = http_get(WS + '/friends/' + str(id1), token=token1)
    friends[id1] = []
    print (r.json())
    for f in r.json():
        friends[id1].append(f['pk_user_id'])
# print(friends)




print(BLUE + 'POST ' + str(NB_POSTS) + ' MESSAGES' + NORMAL)
for id1, e1 in users:
    dummy, token1, rt = successfulLogin(e1, password)
    for i in range(0, NB_POSTS):
        # print(id1, 'to', id2)
        id2 = friend_of(id1)
        r = http_post(WS + '/post/', {
           "body": 'body',
           "to_user_id": id2,
           "tags": [tag(), tag(), tag()]
           }, token=token1)



print(BLUE + 'LIKE TAGS' + NORMAL)
for id1, e1 in users:
    dummy, token1, rt = successfulLogin(e1, password)
    start = 0
    # RETRIEVE FLOW
    r = http_get(WS + '/flow/' + str(start), token=token1)
    o = r.json()
    print (o)
    o = o['posts']
    while len(o) > 0:
        # print(o)

        for p in o:
            for i in range(1, 4):
                if random.randint(1, 3) > 1:  # about 2 times over 3
                    post_id = p['pk_post_id']
                    dummy = http_post(WS + '/like/' + str(post_id) + '/' + str(i), {}, token=token1)
                    # print('like', post_id, i)

        start = start + len(o)
        r = http_get(WS + '/flow/' + str(start), token=token1)
        o = r.json()
        o = o['posts']


# sql('select count(*) from users;', 'USERS')
# sql('select count(*) from friendships;', 'FRIENDSHIPS')
# sql('select count(*) from posts;', 'POSTS')
# sql('select count(*) from flows;', 'FLOWS')
# sql('select count(*) from htags_likes;', 'FLOWS')

if delete_users:
    for id, dummy in users:
        print('delete user', id)
        r = http_delete(WS + '/noauth/user/' + str(id), None, login='TemprAdmin', password='AdminPassword')
else:
    print('users not deleted')

print("Total time: %.2fs" % round(time.time() - start_time))

log_info('%s: %d users  %d friendships  %d posts' % (setting_name, NB_USERS, NB_FRIENDSHIPS, NB_POSTS))
