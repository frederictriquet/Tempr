#!/usr/bin/env python
# -*- coding: UTF-8

##################
import logging
import time

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


log = Log('stats_to_dashboard', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))


con = db_connect(conf.TEMPR_DB2_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
data = db_retrieve(con, 'select date(now()), count(*) from users where signup_date = current_date')

# nb signups
con_dash = db_connect(conf.TEMPR_DBDASHBOARD_HOST, conf.TEMPR_DBDASHBOARD_NAME, conf.TEMPR_DBDASHBOARD_USER)
cur = con_dash.cursor()
params = data[0]
cur.execute('DELETE FROM signups WHERE pk_signup_date = %s', (params[0],))
cur.execute('INSERT INTO signups (pk_signup_date, nb_signups_total) VALUES(%s, %s)', params)
cur.close()
con_dash.commit()


# nb friendships
#sql = "select fk_user_id1, date(now()), count(*) from friendships group by fk_user_id1 order by fk_user_id1"
sql = "select u.pk_user_id, current_date, coalesce(nb_friends,0) \
        from users u \
        left join ( \
          select f.fk_user_id1, count(*) as nb_friends \
            from friendships f \
            group by f.fk_user_id1 \
        ) f_ on f_.fk_user_id1 =  u.pk_user_id \
        order by u.pk_user_id"
#            where start_ts <= %s \
cur = con_dash.cursor()
for params in db_retrieve_yield(con, sql):
    cur.execute('DELETE FROM friends WHERE ck_user_id = %s AND ck_date = %s', (params[0],params[1]))
    cur.execute('INSERT INTO friends (ck_user_id, ck_date, nb_friends_today) VALUES(%s, %s, %s)', params)
    log.debug(params)
con_dash.commit()
con_dash.close()
con.close()