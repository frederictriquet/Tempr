#!/usr/bin/env python
# -*- coding: UTF-8

import logging, sys

execfile('lib/utils.py')


root = logging.getLogger()
root.setLevel(logging.DEBUG)

ch = logging.StreamHandler(sys.stdout)
ch.setLevel(logging.DEBUG)
formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s')
ch.setFormatter(formatter)
root.addHandler(ch)

requests_log = logging.getLogger("requests")
requests_log.addHandler(ch)
requests_log.propagate = False

show_ok = True
WS_URL = get_ws_url()

r = http_get(WS_URL + '/test/')

show(r)
