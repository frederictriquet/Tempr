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


# ## TODO CLEAN THIS CODE
def sendmail(task):
    if conf.USE_SANDBOX:
        log.debug('Sandboxed, no mail sent')
        return
    if task['subaction'] == 'confirm':  # CONFIRM E-MAIL ADDRESS
        from_ = 'Tempr No-reply <noreply@tempr.me>'
        subject = '[Tempr] Please confirm your e-mail address'
        url = conf.WWW_TEMPR_ME + 'confirm/' + task['token']
        html = '<html>Click here: <a href="' + url + '">' + url + '</a></html>'
        r = requests.post(
                          "https://api.mailgun.net/v3/tempr.me/messages",
                          auth=("api", "key-99125d6b68c3a6cbe2355172f5ffd002"),
                          data={"from": from_,
                                "to": [ task['to']],
                                "subject": subject,
                                "html": html}
                          )
    elif task['subaction'] == 'resetpass':
        from_ = 'Tempr No-reply <noreply@tempr.me>'
        subject = '[Tempr] Did you lose your password?'
        url = conf.WWW_TEMPR_ME + 'reset/' + task['token']
        html = '<html>Click here: <a href="' + url + '">' + url + '</a></html>'
        r = requests.post(
                          "https://api.mailgun.net/v3/tempr.me/messages",
                          auth=("api", "key-99125d6b68c3a6cbe2355172f5ffd002"),
                          data={"from": from_,
                                "to": [ task['to']],
                                "subject": subject,
                                "html": html}
                          )
        log.debug(str(r))
    elif task['subaction'] == 'new_user':
        from_ = 'Tempr No-reply <noreply@tempr.me>'
        to_ = 'frederic.triquet+newtempr@gmail.com'
        subject = '[Tempr] New user: '+str(task['nb'])
        html = '<html>' + task['name'] + '</html>'
        r = requests.post(
                          "https://api.mailgun.net/v3/tempr.me/messages",
                          auth=("api", "key-99125d6b68c3a6cbe2355172f5ffd002"),
                          data={"from": from_,
                                "to": [ to_ ],
                                "subject": subject,
                                "html": html}
                          )
        log.debug(str(r))
        


def callback(ch, method, properties, body):
    log.debug(" [x] Received %r" % body)
    try:
        task = json.loads(body)
    except ValueError:
        log.error("Not JSON")
        return
    if task['action'] == 'sendmail':
        sendmail(task)
        pass
    else:
        log.debug("Unknown action")



try:
    logging.getLogger("pika").setLevel(logging.WARNING)
    log = Log('sendmail', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
    log.debug("BEGIN SEND MAIL *************")
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
    channel = connection.channel()
    channel.queue_declare(queue='mail')
    channel.basic_consume(callback,
                          queue='mail',
                          no_ack=True)

    channel.start_consuming()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
finally:
    log.debug("END SEND MAIL ****************")
