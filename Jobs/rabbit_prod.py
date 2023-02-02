#!/usr/bin/env python
# -*- coding: UTF-8

import json
import logging, pika
import sys
import traceback

from tempr.log import Log


log = Log('rabbit_prod', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))

n = 0
try:
    while True:
        n = n+1
        cred = pika.credentials.PlainCredentials('lapin', 'lapin')
        connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
        channel = connection.channel()
        channel.queue_declare(queue='test')
        params = {'k': n}
        channel.basic_publish(exchange='', routing_key='test', body=json.dumps(params))
        if (n % 1000 == 0):
            log.debug(str(n))
        connection.close()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
