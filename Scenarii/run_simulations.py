#!/usr/bin/env python
# -*- coding: UTF-8

import time, os

execfile('lib/log_lib.py')
execfile('lib/conf.py')
execfile('lib/pg_lib.py')

log_init('simu.log', logging.INFO)

log_info('--- STARTING SIMULATIONS --');

con = db_connect(DB_HOST, DB_NAME, DB_USER)
simu_types = [ 'nano', 'micro', 'mini', 'tiny', 'small', 'medium', 'big', 'huge']
simu_types = [ 'medium', 'big']
simu_types = [ 'medium']
for t in simu_types:
    log_info('---------------------------');
    os.system('../Various/sync_db.sh > /dev/null')
    start = time.time()
    os.system('./simulation.py ' + str(t))
    log_info("simulation time: %.2fs" % round(time.time() - start))

    posts_htags = db_retrieve(con, 'select count(*) from posts_htags')[0][0]
    htags_likes = db_retrieve(con, 'select count(*) from htags_likes')[0][0]

    log_info('%d posts_htags   %d htags_likes' % (posts_htags, htags_likes))

    start = time.time()
    os.system('../DB/jobs/update_user_trends.py')
    log_info("update user trends time: %.2fs" % round(time.time() - start))


# os.system('../Various/sync_db.sh > /dev/null')
# os.system('./simulation.py mini')

log_info('---------------------------');
log_info('simulations done')
