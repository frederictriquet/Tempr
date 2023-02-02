#!/usr/bin/env python
# -*- coding: UTF-8

##################
import json
import logging
import pika
import time

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


def event_create(channel, post_id):
    e = {'type': 'post_by_phone', 'post_id': post_id}
    channel.basic_publish(exchange='',
                      routing_key='events',
                      body=json.dumps(e))


con = db_connect(conf.TEMPR_DB2_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
sql = "select * from view_pending_posts_by_sms where pending_reason=B'00011000' " + \
    "and phone is not null and nb_reminds < 4 " + \
    "and creation_ts < NOW() - interval '2days' " + \
    "and (last_remind_date is null or last_remind_date < current_date-interval '2days') limit 2"
posts = db_retrieve(con, sql)
con.close()

logging.getLogger("pika").setLevel(logging.WARNING)
log = Log('remind_sms', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
log.debug('START remind_sms')
cred = pika.credentials.PlainCredentials('lapin', 'lapin')
connection = pika.BlockingConnection(pika.ConnectionParameters(conf.TEMPR_MQ_HOST, credentials=cred))
channel = connection.channel()
channel.queue_declare(queue='events')

for p in posts:
    log.debug(p)
    event_create(channel, p[0])


channel.close()
log.debug(str(len(posts)) + ' SMS sent again')
log.debug('END remind_sms')
