#!/usr/bin/env python
# -*- coding: UTF-8

##################
import logging
import time

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


log = Log('purge_confirmed_users', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))


con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
nb_phone_users = db_retrieve(con, 'select * from purge_confirmed_users_phone()')
nb_facebook_users = db_retrieve(con, 'select * from purge_confirmed_users_facebook()')
con.commit()
con.close()

log.debug('pending users removed (phone): '+str(nb_phone_users[0][0]))
log.debug('pending users removed (facebook): '+str(nb_facebook_users[0][0]))

