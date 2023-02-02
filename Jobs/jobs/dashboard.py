#!/usr/bin/env python
# -*- coding: UTF-8

import psycopg2
from tempr import conf
from tempr.pg_lib import *

#INSCRIPTION

con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
day = db_retrieve(con, 'select date(now())')
user = db_retrieve(con, 'select count(*) from users')
con.close()

con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DBDASHBOARD_NAME, conf.TEMPR_DBDASHBOARD_USER)
cur = con.cursor()
params = (day[0][0], user[0][0])
cur.execute('INSERT INTO signups (pk_signup_date, nb_signups_total) VALUES(%s, %s)', params)
con.commit()
con.close()

#Lien AMITIE

con1 = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
sql = "select fk_user_id1, date(now()), count(*) from friendships group by fk_user_id1 order by fk_user_id1"

con2 = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DBDASHBOARD_NAME, conf.TEMPR_DBDASHBOARD_USER)
cur = con2.cursor()
for friend in db_retrieve_yield(con1, sql):
    params = (friend[0], friend[1], friend[2])
    cur.execute('INSERT INTO friends (ck_user_id, ck_date, nb_friends_today) VALUES(%s, %s, %s)', params)
con2.commit()
con2.close()
con1.close()
