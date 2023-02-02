#!/usr/bin/env python
# -*- coding: UTF-8

import requests, json
import urllib, logging


def http_post(url, body, certfile, keyfile):
    headers = {'content-type': 'application/json', 'accept': 'application/json'}
    r = requests.post(url, headers=headers, json=body, allow_redirects=False, cert=(certfile, keyfile))
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

WS = 'https://api.development.push.apple.com/3/device/'

data = { "aps" : { "alert" : "Hello" } }
device_id = ''
r = http_post(WS + device_id, data, certfile, keyfile)
show(r)
