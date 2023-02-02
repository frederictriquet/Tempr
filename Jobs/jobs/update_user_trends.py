#!/usr/bin/env python
# -*- coding: UTF-8

##################
import logging
import pika, traceback, sys, json
import redis
import time

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


def process_trends(r, con, sql, user_id, prefix):
    global r_old                # remove
    recent_trends = db_retrieve(con, sql, params=(user_id,))
    new_rt = json.dumps(recent_trends)
    k = prefix+str(user_id)
    if r_old.exists(k):            # change
        rt = r_old.get(k)          # change
    else:
        rt = None
    if new_rt != rt:
        r_old.set(k, new_rt, 600)
        r.set(k, new_rt)
        return True
    else:
        return False

def event_create(channel, user_id):
    e = {'type': 'stats_updated', 'to_id': user_id}
    channel.basic_publish(exchange='',
                      routing_key='events',
                      body=json.dumps(e))


start = time.time()

try:
    logging.getLogger("pika").setLevel(logging.WARNING)
    log = Log('update_user_trends', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
    log.debug('START update_user_trends')
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters(conf.TEMPR_MQ_HOST, credentials=cred))
    channel = connection.channel()
    channel.queue_declare(queue='events')

    r = redis.StrictRedis(host=conf.TEMPR_STORE_HOST, db=conf.TEMPR_STORE_TRENDS_DB)
    r_old = redis.StrictRedis(host=conf.TEMPR_STORE_HOST)

    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    sql = 'select * from users'
    #print db_retrieve(con, sql)

    cur = con.cursor()
    for obj in db_retrieve_yield(con, sql):
        user_id = obj[0]
        params = (user_id,)
        print user_id
        db_call(cur, 'trend_user_update', params=params )
        con.commit()
        db_call(cur, 'trend_user_long_term_update', params=params )
        con.commit()
    
        sql = 'select * from view_user_recent_trends where fk_user_id = %s order by pop desc, tag asc limit 5'
        rt = process_trends(r, con, sql, user_id, 'r')
        sql = 'select * from view_user_long_term_trends where fk_user_id = %s order by pop desc, tag asc limit 5'
        lt = process_trends(r, con, sql, user_id, 'l')
    
        if rt or lt:
            event_create(channel, user_id)
    cur.close()
    channel.close()
    connection.close()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
finally:
    log.debug("Total time: %.2fs" % round(time.time() - start))
    log.debug('END update_user_trends')

