#!/usr/bin/env python
# -*- coding: UTF-8

##################
# import time

import logging
import pika, json, requests
import sys
import traceback

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *
import tempr.pidfile

def get_3_tags():
    return ('NewUser','Fantastic','Awesome')

def new_user(event):
    log.debug('POST A TEMP '+str(event))
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    # creer 3 tags
    # creer post
    #sql = "select * from post_create_pending(%s,%s,%s,%s,%s,%s,%s,%s)"
    sql = "select * from posts_create(%s,%s,%s,%s,%s,%s,%s)"
    body = "Welcome! Touch the #hashtags above to vote for them. Set your phone number, link your Facebook account and post messages to your friends and enjoy!"
    from_user_id = 1 # Tempr App
    to_user_id = int(event['user_id'])
    tag1, tag2, tag3 = get_3_tags()
    city_id = None
    #pending_reason = 1
    #params = (body, from_user_id, to_user_id, tag1, tag2, tag3, city_id, pending_reason)
    params = (body, from_user_id, to_user_id, tag1, tag2, tag3, city_id)
    res = db_retrieve(con, sql, params)
    log.debug(str(res))
    con.commit()
    con.close()
    res = res[0]
    if (res[0] == 'post'):
        post_id = res[1]
        e = { 'type': 'post', 'post_id': post_id }
        eventschannel.basic_publish(exchange='',
                      routing_key='events',
                      body=json.dumps(e))
    else:
        log.error('post failure '+str(res))


def callback(ch, method, properties, body):
    log.debug(" [x] Received %r" % body)
    try:
        event = json.loads(body)
    except ValueError:
        log.error("Not JSON "+str(body))
        return
    new_user(event)

try:
    logging.getLogger("pika").setLevel(logging.WARNING)
    log = Log('temps', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
    log.debug("BEGIN SEND TEMPS *************")
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
    eventschannel = connection.channel()
    eventschannel.queue_declare(queue='events')
    channel = connection.channel()
    channel.queue_declare(queue='temps')
    channel.basic_consume(callback,
                          queue='temps',
                          no_ack=True)

    channel.start_consuming()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
finally:
    log.debug("END SEND TEMPS ****************")
