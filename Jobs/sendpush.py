#!/usr/bin/env python
# -*- coding: UTF-8

##################
# import time

from apns import APNs, Frame, Payload, PayloadAlert
import logging
import pika, json, requests
import redis
import sys
import traceback

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *
import tempr.pidfile


events = { 'post': ('notification.new_qualities','pn_postaboutyou', 3*60),
           'friendship_request': ('notification.invitation_received','pn_friendshiprequest', 3*60),
           'friendship_acceptance':('notification.invitation_accepted','pn_frienshipacceptance', 3*60),
           'i_am_liked':('notification.qualities_appreciated','pn_like', 3*60),
           'they_like_it':('notification.post_appreciated','pn_like', 3*60),
           'comment':('notification.received_comment','pn_comment', 3*60),
           'stats_updated':('notification.profile_evolved','pn_profileupdated', 3600*24),
           'system':('Hello from Tempr', None, 1)}

def sendpush(event):
    global apns
    log.debug('SEND PUSH '+str(event))
    # recuperer les iosdevice du to_id
    if event['to_id'] == None:
        log.error('to_id est NONE')
    else:
        to_id = int(event['to_id'])
        con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
        event_def = events[event['type']]
        body = event_def[0]
        k = event['type'] + str(to_id)
        if r.exists(k):
            log.debug('skipping')
            return
        r.setex(k, event_def[2], None)
        pn_field = event_def[1]
        if pn_field is not None:
            sql = "select ios_id from iosdevices \
                    join users on pk_user_id = fk_user_id and "+pn_field+"\
                    where fk_user_id=%s"
        else:
            sql = "select ios_id from iosdevices \
                    join users on pk_user_id = fk_user_id \
                    where fk_user_id=%s"
        #log_debug(sql)
        payloadAlert = PayloadAlert(loc_key=body)
        for token in db_retrieve(con, sql,(to_id,)):
            payload = Payload(
                          alert=payloadAlert,
                          sound="default",
                          badge=1)
            try:
                apns.gateway_server.send_notification(token[0], payload)
            except Exception, e:
                exc_type, exc_value, exc_traceback = sys.exc_info()
                log.error(str(e))
                log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
                apns = APNs(use_sandbox=use_sandbox, cert_file=cert_file, key_file=key_file)
                apns.gateway_server.send_notification(token[0], payload)
                log.error('notification sent again after reconnect')


def callback(ch, method, properties, body):
    log.debug(" [x] Received %r" % body)
    try:
        event = json.loads(body)
    except ValueError:
        log.error("Not JSON "+str(body))
        return
    sendpush(event)

cert_file = conf.CERT_FILE
key_file = conf.KEY_FILE
use_sandbox = conf.USE_SANDBOX

try:
    logging.getLogger("pika").setLevel(logging.WARNING)
    log = Log('sendpush', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
    log.debug("BEGIN SEND PUSH *************")
    apns = APNs(use_sandbox=use_sandbox, cert_file=cert_file, key_file=key_file)
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
    channel = connection.channel()
    channel.queue_declare(queue='push')
    channel.basic_consume(callback,
                          queue='push',
                          no_ack=True)

    r = redis.StrictRedis(host=conf.TEMPR_STORE_HOST, db=conf.TEMPR_STORE_EVENTS_AND_PUSH)
    
    channel.start_consuming()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
finally:
    log.debug("END SEND PUSH ****************")
