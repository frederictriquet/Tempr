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


def new_user_send(user_id):
    e = {'type': 'new_user', 'user_id': user_id}
    tempschannel.basic_publish(exchange='',
                      routing_key='temps',
                      body=json.dumps(e))


def event_send(user_id):
    e = {'type': 'new_user', 'from_id': user_id}
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
    tempschannel = connection.channel()
    tempschannel.queue_declare(queue='temps')
    #new_user_send(33)
    event_send(65)

except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    print(str(e))
    print(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
