#!/usr/bin/env python
# -*- coding: UTF-8

##################
# import time

import logging
import pika, json, requests, traceback
import sys

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


def event_send(eventtype, from_id=None, to_id=None, post_id=None):
    e = {'type': eventtype, 'from_id': from_id, 'to_id': to_id, 'post_id': post_id}
    channel.basic_publish(exchange='',
                      routing_key='events',
                      body=json.dumps(e))




################################################################################################################



try:
    logging.getLogger("pika").setLevel(logging.WARNING)
    
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
    channel = connection.channel()
    channel.queue_declare(queue='events')

    event_send('fb_connected', from_id=32, to_id=None, post_id=None)

except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    print(str(e))
    print(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
