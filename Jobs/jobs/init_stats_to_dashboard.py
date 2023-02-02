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
dates = db_retrieve(con,"select generate_series('2016-06-24'::date,now()::date, interval '1day')::date")

con_dash = db_connect(conf.TEMPR_DBDASHBOARD_HOST, conf.TEMPR_DBDASHBOARD_NAME, conf.TEMPR_DBDASHBOARD_USER)
cur_dash = con_dash.cursor()
cur_dash.execute('DELETE FROM signups')

for d in dates:
    data = db_retrieve(con, 'select count(*) from users where signup_date = %s', (d,))
    params = (d, data[0])
    cur_dash.execute('INSERT INTO signups (pk_signup_date, nb_signups_total) VALUES(%s, %s)', params)
cur_dash.close()
con_dash.commit()
    



# nb friendships


#sql = "select fk_user_id1, start_ts::date as day, count(fk_user_id2) from friendships group by day, fk_user_id1 order by fk_user_id1, day"
cur_dash = con_dash.cursor()
cur_dash.execute('DELETE FROM friends')
for d in dates:
    # sql = "select fk_user_id1, count(fk_user_id2) from friendships where start_ts <= %s group by fk_user_id1 order by fk_user_id1;"
    sql = "select u.pk_user_id, coalesce(nb_friends,0) \
            from users u \
            left join ( \
              select f.fk_user_id1, count(*) as nb_friends \
                from friendships f \
                where start_ts <= %s \
                group by f.fk_user_id1 \
            ) f_ on f_.fk_user_id1 =  u.pk_user_id \
            order by u.pk_user_id"
    params = (d,)
    for f in db_retrieve_yield(con, sql, params):
        params2 = (f[0], d, f[1])
        cur_dash.execute('INSERT INTO friends (ck_user_id, ck_date, nb_friends_today) VALUES(%s, %s, %s)', params2)
        log.debug(params2)
con_dash.commit()
con_dash.close()
con.close()