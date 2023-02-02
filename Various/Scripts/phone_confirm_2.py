#!/usr/bin/env python
# -*- coding: UTF-8

import requests, json
import urllib, logging


def http_post(url, body, login=None, password=None, token=None):
    headers = {'content-type': 'application/json', 'accept': 'application/json'}
    if login != None and password != None:
        auth = (login, password)
    else:
        auth = None
    if token != None:
        headers.update({'Authorization':'Bearer ' + token})

    r = requests.post(url, headers=headers, json=body, auth=auth, allow_redirects=False, verify=False)
    r.encoding = 'UTF-8'
    return r

def show(r, jsonPretty=False):
    indent = None
    if jsonPretty:
        indent = 4
    try:
        print 'HTTP code:', r.status_code
        if r.status_code < 300:
            print 'headers:', r.headers
            if 'content-length' in r.headers.keys():
                print 'content-lenght:', r.headers['content-length']
            else:
                print 'NO content-length'
            if 'content-type' in r.headers.keys():
                print 'content-type:', r.headers['content-type']
                if r.headers['content-type'] == 'application/json':
                    print "******* Response body\n", json.dumps(r.json(), indent=indent), "\n******* END"
            else:
                print 'NO content-type'
        else:
            print r.headers
            print r.text
    except Exception:
        traceback.print_exc()
        # print r.text
        pass


requests_log = logging.getLogger("requests")
requests_log.addHandler(logging.NullHandler())
requests_log.propagate = False
import urllib3
urllib3.disable_warnings()
logging.captureWarnings(True)

WS = 'https://ws.preprod.tempr.me'

email = "fred@tempr.co"
password = 'trop14st'

r = http_post(WS + '/login/', {
                                  "email": email,
                                  "password": password
    })
# show(r)
token = r.json()['token']

r = http_post(WS + '/profile/confirm/phone/1000', {}, token=token)
show(r)
# r = http_post(WS + '/profile/confirm/phone/4515', {}, token=token)
# show(r)
# r = http_post(WS + '/profile/confirm/phone/3268', {}, token=token)
# show(r)
# r = http_post(WS + '/profile/confirm/phone/1000', {}, token=token)
# show(r)

# sql('select * from phone_confirmations')
# sql('select * from users')
# DELETE USER
# deleteUser(pk_user_id)
