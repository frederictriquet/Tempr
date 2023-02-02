#!/usr/bin/env python
# -*- coding: UTF-8

##################
# import time

import logging
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
#     url = "http://www.sms-lowcost.com/cgi-bin/?keyid=3ab3af6b781a46aef32653bb96619826&sms=" + smsbody + "&num=" + to + "&emetteur=Tempr"
    url = "https://smsapi.free-mobile.fr/sendmsg?user=16639168&pass=A7jKmCCbFg9xNl&msg="+ smsbody

    r = requests.get(url)
    log.debug(url)
    log.debug(r.status_code)
    log.debug(r.text)
    requests.post(
                    "https://api.mailgun.net/v3/tempr.me/messages",
                    auth=("api", "key-99125d6b68c3a6cbe2355172f5ffd002"),
                    data={"from": from_,
                          "to": [ 'frederic.triquet+tempr@gmail.com' ],
                          "subject": smsbody,
                          "html": smsbody + "<hr/>" + url}
                    )

def sendsms2(to, body):
    data= {'key': 'df91e8ca0325291b0ec77a47591d4942',
           'destinataires': to,
           'type': 'lowcost', #premium',
           'message': body,
           'expediteur': 'Tempr',
           'encodage': 'auto'
           }
    url = 'http://www.spot-hit.fr/api/envoyer/sms'
    r = requests.post(url, data=data)
    log.debug(r.status_code)
    log.debug(r.text)

def confirm(task):
    code = task['code']
    body = "Tempr - PinCode - " + str(code)
    sendsms(task['to'], body)

def post(task, post_key):
    # 1234567890 invites you on Tempr app, telling you are #1234567891234567890123456789. Join and get fun. LINK123456789012345678901234567890
    # PRENOM     invites you on Tempr app, telling you are #QUALITE                     . Join and get fun. https://tempr.me/s/678901234567890
    log.debug(task)

    firstname = emoji_pattern.sub(r'',task['firstname']).encode('utf-8')
    tag = emoji_pattern.sub(r'',task['tag']).encode('utf-8')
    #body = '{0} invites you on Tempr App, telling you are #{1}. Join and get fun. https://tempr.me/s/{2}'.format(firstname,tag, k)
    body = '{0}'.format(firstname)
    sendsms(task['phone'], body)
    #sendsms2(task['phone'], body)

def callback(body, post_key):
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
            post(task, post_key)
        else:
            print("Unknown action")
    except Exception, e:
        exc_type, exc_value, exc_traceback = sys.exc_info()
        log.debug(str(e))
        log.debug(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))


emoji_pattern = re.compile("["
        u"\U0001F600-\U0001F64F"  # emoticons
        u"\U0001F300-\U0001F5FF"  # symbols & pictographs
        u"\U0001F680-\U0001F6FF"  # transport & map symbols
        u"\U0001F1E0-\U0001F1FF"  # flags (iOS)
        u"\U00002600-\U000026FF"  # flags (iOS)
                           "]+", flags=re.UNICODE)
try:
    log = Log('removeme', '.', global_level=logging.DEBUG, levels=(logging.DEBUG,))
    log.debug("BEGIN SEND SMS *************")
    callback('{"action": "post", "phone": "+33689824777", "tag": "gentille", "firstname": "PrettyCocoonOfLau\\u2615\\ufe0f", "post_id": 275}', '5jEahwWUVftZsLI')
    #callback('{"action": "post", "phone": "+33689824777", "tag": "test", "firstname": "coffee\u2615\ufe0f", "post_id": 275}', '5jEahwWUVftZsLI')
    #callback('{"action": "post", "phone": "+33689824777", "tag": "test", "firstname": "エトワール     Lau", "post_id": 275}', '5jEahwWUVftZsLI')
    #callback('{"action": "post", "phone": "+33689824777", "tag": "test", "firstname": "エトワール     Lau\u2615\ufe0f", "post_id": 275}', '5jEahwWUVftZsLI')
    #callback('{"action": "post", "phone": "+33689824777", "tag": "test", "firstname": "çéàïè", "post_id": 275}', '5jEahwWUVftZsLI')
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    print(str(e))
    print(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
finally:
    log.debug("END SEND SMS ****************")


