#!/usr/bin/env python
# -*- coding: UTF-8

##################
# import time

import logging
from lxml import objectify
import pika, json, requests
import random
import re
import redis
import sys
import traceback
import urllib

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


def sendsms(to, body):
    from_ = 'Tempr No-reply <noreply@tempr.me>'
    smsbody = urllib.quote(body)
    log.debug(to + ' - ' + smsbody)
#    return
    url = "http://www.sms-lowcost.com/cgi-bin/?keyid=3ab3af6b781a46aef32653bb96619826&sms=" + smsbody + "&num=" + to + "&emetteur=Tempr"
    r = requests.get(url)
    log.debug(r.status_code)
    log.debug(r.text)
    try:
        xml = re.sub(r'\bencoding="[-\w]+"', '', r.text, count=1)
        xml_object = objectify.fromstring(xml)
        code = xml_object.__dict__['code']
        return_msg = str(xml_object.__dict__['tel']) + '<br/>' + xml_object.__dict__['message']
    except:
        code = -1
        return_msg = 'erreur avec sms-lowcost'
        pass
    try:
        requests.post(
                      "https://api.mailgun.net/v3/tempr.me/messages",
                      auth=("api", "key-99125d6b68c3a6cbe2355172f5ffd002"),
                      data={"from": from_,
                            "to": [ 'frederic.triquet+tempr@gmail.com' ],
                            "subject": '[' + str(code) + '] ' + body,
                            "html": body.encode('utf-8') + "<hr/>" + url + "<hr/>" + return_msg }
                      )
    except Exception, e:
        exc_type, exc_value, exc_traceback = sys.exc_info()
        log.error("SEND MAIL ERROR: "+str(e))
        log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))

def confirm(task):
    code = task['code']
    data = json.dumps({'id': task['user_id'], 'phone': task['to']})
    # TODO factoriser
    alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.'
    r = redis.StrictRedis(host=conf.TEMPR_STORE_HOST, db=conf.TEMPR_STORE_CONFIRMPHONE_DB)
    while True:
        k = ''.join(random.sample(alphabet,15))
        if not r.exists(k):
            r.setex(k, 86400*14, data)
            break
    body = "Tempr - PinCode - {0} - https://tempr.me/c/{1}".format(code, k)
    sendsms(task['to'], body)

def post(task):
    # 1234567890 invites you on Tempr app, telling you are #1234567891234567890123456789. Join and get fun. LINK123456789012345678901234567890
    # PRENOM     invites you on Tempr app, telling you are #QUALITE                     . Join and get fun. https://tempr.me/s/678901234567890
    log.debug(task)
    # TODO factoriser
    alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.'
    r = redis.StrictRedis(host=conf.TEMPR_STORE_HOST, db=conf.TEMPR_STORE_POSTSMS_DB)
    while True:
        k = ''.join(random.sample(alphabet,15))
        if not r.exists(k):
            r.setex(k, 86400*14, task['post_id'])
            break
    firstname = emoji_pattern.sub(r'',task['firstname']).encode('utf-8')
    tag = emoji_pattern.sub(r'',task['tag']).encode('utf-8')
    body = '{0} invites you on Tempr App, telling you are #{1}. Join and get fun. https://tempr.me/s/{2}'.format(firstname,tag, k)
    sendsms(task['phone'], body)

def callback(ch, method, properties, body):
    log.debug(" [x] Received %r" % body)
    try:
        task = json.loads(body)
    except ValueError:
        log.debug("Not JSON")
        return
    try:
        if task['action'] == 'sendsms':
            confirm(task)
        elif task['action'] == 'post':
            post(task)
        else:
            log.debug("Unknown action")
    except Exception, e:
        exc_type, exc_value, exc_traceback = sys.exc_info()
        log.error(str(e))
        log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))


emoji_pattern = re.compile("["
        u"\U0001F600-\U0001F64F"  # emoticons
        u"\U0001F300-\U0001F5FF"  # symbols & pictographs
        u"\U0001F680-\U0001F6FF"  # transport & map symbols
        u"\U0001F1E0-\U0001F1FF"  # flags (iOS)
        u"\U00002600-\U000026FF"  # tasse de café et autres
                           "]+", flags=re.UNICODE)
try:
    logging.getLogger("pika").setLevel(logging.WARNING)
    log = Log('sendsms', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
    log.debug("BEGIN SEND SMS *************")
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
    channel = connection.channel()
    channel.queue_declare(queue='sms')
    channel.basic_consume(callback,
                          queue='sms',
                          no_ack=True)

    channel.start_consuming()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
finally:
    log.debug("END SEND SMS ****************")
