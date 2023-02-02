#!/usr/bin/env python
# -*- coding: UTF-8

##################
# import time

from apns import APNs
import logging

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


cert_file = conf.CERT_FILE
key_file = conf.KEY_FILE
use_sandbox = conf.USE_SANDBOX



# New APNS connection
feedback_connection = APNs(use_sandbox=use_sandbox, cert_file=cert_file, key_file=key_file)
log = Log('purge_push_tokens', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
# Get feedback messages.
for (token_hex, fail_time) in feedback_connection.feedback_server.items():
    # do stuff with token_hex and fail_time
    log.debug('Removing ios device '+token_hex+' ('+ str(fail_time)+')')
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    cur = con.cursor()
    cur.execute('delete from iosdevices where ios_id=%s', (token_hex.upper(),))
    cur.close()
    con.commit()