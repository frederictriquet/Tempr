#!/usr/bin/env python
# -*- coding: UTF-8

##################
import requests, json, os, logging, random, time

# deactivate logging to prevent Requests from polluting my logs
requests_log = logging.getLogger("requests")
requests_log.addHandler(logging.NullHandler())
requests_log.propagate = False

def get_ws_url():
    return os.getenv('WS_URL', 'https://ws.local.tempr.me')

def get_email():
    return str(random.randint(0, 10000)) + '@a.fr'

def get_password():
    return 'password'  # 'Myp4ssword'

def sql(req, ip="172.16.100.10",title=None):
#    return
    if title != None:
        print(BLUE + title + NORMAL)
    try:
        os.system("PAGER=cat psql -h "+ip+" -U postgres tempr -c '" + req.replace("'", "'\\''") + "'")
    except:
        print('Cannot connect to DB')
        pass

def http_put(url, body, login=None, password=None, token=None):
    headers = {'content-type': 'application/json', 'accept': 'application/json'}
    if login != None and password != None:
        auth = (login, password)
    else:
        auth = None
    if token != None:
        headers.update({'Authorization':'Bearer ' + token})

    r = requests.put(url, headers=headers, json=body, auth=auth, allow_redirects=False, verify=False)
    r.encoding = 'UTF-8'
    return r

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

def http_post_file(url, filename, login=None, password=None, token=None):
    headers = {}
    if login != None and password != None:
        auth = (login, password)
    else:
        auth = None
    if token != None:
        headers.update({'Authorization':'Bearer ' + token})
    files = {'file': open(filename, 'rb')}
    r = requests.post(url, headers=headers, files=files, auth=auth, allow_redirects=False, verify=False)
    return r

def http_get(url, login=None, password=None, token=None):
    headers = {'accept': 'application/json'}
    if login != None and password != None:
        auth = (login, password)
    else:
        auth = None
    if token != None:
        headers.update({'Authorization':'Bearer ' + token})

    r = requests.get(url, headers=headers, auth=auth, allow_redirects=False, verify=False)
    r.encoding = 'UTF-8'
    return r

def http_delete(url, body, login=None, password=None, token=None):
    headers = {'content-type': 'application/json', 'accept': 'application/json'}
    if login != None and password != None:
        auth = (login, password)
    else:
        auth = None
    if token != None:
        headers.update({'Authorization':'Bearer ' + token})

    r = requests.delete(url, headers=headers, json=body, auth=auth, allow_redirects=False, verify=False)
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


def sleep(s):
    print 'Sleeping for '+str(s)+' seconds'
    time.sleep(s)
