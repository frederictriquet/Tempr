#!/usr/bin/env python
# -*- coding: UTF-8

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')


WS = get_ws_url()
email1 = "a@a.com"
pass1 = get_password()

pk_user_id1, login1 = createUser(email1, "aaa", "bbb", pass1)

# SUCCESSFUL LOGIN
pk_user_id1, token1, rt1 = successfulLogin(email1, pass1)


checkFlowNone(pk_user_id1, token1)

postRaw("Post with GPS coords", token1, pk_user_id1, tag1="tag", locality='Lille', countryCode='FR')
postRaw("Post with GPS coords", token1, pk_user_id1, tag1="tag", locality='Lille', countryCode='FR')
#os.system("PAGER=cat psql -h 172.16.100.10 -U postgres tempr -c 'select * from posts;'")
#os.system("PAGER=cat psql -h 172.16.100.10 -U postgres tempr -c 'select * from devcities;'")


postRaw("Post with GPS coords", token1, pk_user_id1, tag1="tag", lat=50.62800243589743, lon=3.0684445016891884)
postRaw("Post with GPS coords", token1, pk_user_id1, tag1="tag", lat=49.875298910256404, lon=-3.0741946790540537)
# print('Message posted')
#os.system("PAGER=cat psql -h 172.16.100.10 -U postgres tempr -c 'select * from posts;'")
# os.system("PAGER=cat psql -h 172.16.1.10 -U postgres tempr -c 'select * from view_decorated_posts;'")
# os.system("PAGER=cat psql -h 172.16.1.10 -U postgres tempr -c 'select * from view_flow_posts;'")

checkFlow(pk_user_id1, token1, 4, 'from me')


# DELETE USERS
deleteUser(token1)

