#!/usr/bin/env python
# -*- coding: UTF-8

from datetime import datetime
import json
import logging
import multiprocessing
import os
import sys
import time

from tempr import conf
from tempr.cron import *
from tempr.log import Log
from tempr.pg_lib import *


# import thread
# import json
# import warnings
now = datetime.now()
now_tuple = now.timetuple()[0:5]


def launch_job(job, now):
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    data = {'is_running': True, 'last_begin_ts': now}
    if job[ACTIVITY] == 'once':
        data['activity'] = 'launched once'
    db_update_obj(con, 'jobs', {'pk_job_id':job[PK_JOB_ID]}, data)
    con.close()
    start = time.time()

    log.debug('-- begin' + str(job[NAME]))
    try:
        filename = './jobs/' + job[NAME] + '.py'
        os.chdir('/srv/Tempr/Jobs/')
        execfile(filename)
    except IOError:
        con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
        log.error(traceback.format_exc())
        db_update_obj(con, 'jobs', {'pk_job_id':job[PK_JOB_ID]}, {'is_running': False, 'status': traceback.format_exc()})
        con.close()
        pass
    log.debug('-- end' + str(job[NAME]))

    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    db_update_obj(con, 'jobs', {'pk_job_id':job[PK_JOB_ID]}, {'is_running': False, 'last_duration': int(time.time() - start)})
    con.close()
    sys.exit(0)



log = Log('scheduler', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))


# now = time.strftime('%Y-%m-%d %H:%M:00')
# minutes = int(time.strftime('%M'))
PK_JOB_ID = 0
NAME = 1
DESCR = 2
ACTIVITY = 3
CRONTAB = 4
LAST_BEGIN_TS = 5
LAST_DURATION = 6
IS_RUNNING = 7
STATUS = 8

con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
log.debug("BEGIN SCHEDULING *************")

for i in db_retrieve_yield(con, "select * from jobs where activity='active' or activity='once' order by name"):
    try:
        cte = CronExpression(i[CRONTAB])
    except ValueError, e:
        db_update_obj(con, 'jobs', {'pk_job_id':i[PK_JOB_ID]}, {'status': 'crontab: ' + str(e)})
        continue
    if cte.check_trigger(now_tuple):
        if i[IS_RUNNING]:
            log.debug('too long')
            db_update_obj(con, 'jobs', {'pk_job_id':i[PK_JOB_ID]}, {'status': 'job takes too much time'})
            pass
        else:
            log.debug('launch job' + str(i[0]))

            try:
                pid = os.fork()
                if pid > 0:
                    continue
            except OSError, e:
                log.error("can't fork that bitch")
                continue

            # sys.stdout.flush()
            # sys.stderr.flush()
            # si = file(self.stdin, 'r')
            # so = file(self.stdout, 'a+')
            # se = file(self.stderr, 'a+', 0)
            # os.dup2(si.fileno(), sys.stdin.fileno())
            # os.dup2(so.fileno(), sys.stdout.fileno())
            # os.dup2(se.fileno(), sys.stderr.fileno())
            launch_job(i, datetime(now_tuple[0], now_tuple[1], now_tuple[2], now_tuple[3], now_tuple[4]))


con.close()
log.debug("END SCHEDULING *************")

sys.exit(0)

