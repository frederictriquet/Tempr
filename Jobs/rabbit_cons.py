#!/usr/bin/env python
# -*- coding: UTF-8

import json
import logging, pika
import sys
import traceback

from tempr.log import Log



log = Log('rabbit_cons', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
cred = pika.credentials.PlainCredentials('lapin', 'lapin')
connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
channel = connection.channel()
channel.queue_declare(queue='test')


def callback(ch, method, properties, body):
    x = json.loads(body)
    if (x['k'] % 1000 == 0):
        log.debug(body)


try:
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
    channel = connection.channel()
    channel.queue_declare(queue='test')
    channel.basic_consume(callback, queue='test', no_ack=True)
    channel.start_consuming()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
