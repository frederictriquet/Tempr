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



start = time.time()

try:
    logging.getLogger("pika").setLevel(logging.WARNING)
    log = Log('events_refresh', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
    log.debug('START events_refresh')
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters(conf.TEMPR_MQ_HOST, credentials=cred))
    channel = connection.channel()
    channel.queue_declare(queue='events')

    r = redis.StrictRedis(host=conf.TEMPR_STORE_HOST, db=conf.TEMPR_STORE_EVENTS_AND_PUSH)

    #con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    con2 = db_connect(conf.TEMPR_DB2_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    sql = "select pk_event_id, from_fk_user_id, to_fk_user_id from view_events_old_friendship_requests"

    #print db_retrieve(con, sql)

    cur = con2.cursor()
    for obj in db_retrieve_yield(con2, sql):
        pk_event_id = obj[0]
        from_fk_user_id = obj[1]
        to_fk_user_id = obj[2]
        log.debug(str(obj))
        e = {'type': 'friendship_request', 'from_id': from_fk_user_id, 'to_id': to_fk_user_id}
        channel.basic_publish(exchange='',
                      routing_key='events',
                      body=json.dumps(e))
    cur.close()
    channel.close()
    connection.close()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
finally:
    log.debug("Total time: %.2fs" % round(time.time() - start))
    log.debug('END events_refresh')

