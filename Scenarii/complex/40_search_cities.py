#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()

pass1 = get_password()
pk_user_id1, login1 = createUser('a@a.fr', "aaa", "bbb", pass1)
pk_user_id1, token1, rt = successfulLogin('a@a.fr', pass1)

Lille_FR = (50.63297, 3.05858)
Lille_BE = (51.24197, 4.82313)
La_Chiers = (49.546294, 5.816298)

p = La_Chiers

lat, lon = p

# GET CITIES THROUGH POSTGRESQL
r = http_get(WS + '/cities/pg/' + str(lat) + '/' + str(lon), token=token1)
show(r, True)
r = http_get(WS + '/cities/es/' + str(lat) + '/' + str(lon), token=token1)
show(r, True)


# DELETE USERS
deleteUser(token1)
