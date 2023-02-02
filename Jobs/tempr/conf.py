#!/usr/bin/env python
# -*- coding: UTF-8

# Modify Deploy/Vagrant/roles/jobs_deployment_role/templates/conf.py.j2 accordingly
TEMPR_DB_HOST = '172.16.1.10'
TEMPR_DB2_HOST = '172.16.1.10'
TEMPR_DB_NAME = 'tempr'
TEMPR_DB_USER = 'postgres'

TEMPR_DBDASHBOARD_HOST = '172.16.1.10'
TEMPR_DBDASHBOARD_NAME = 'dbdash'
TEMPR_DBDASHBOARD_USER = 'vierge'

TEMPR_ES_HOST = '172.16.1.10:9200'
WWW_TEMPR_ME = 'https://www.local.tempr.me/'

TEMPR_STORE_HOST = 'localhost'
TEMPR_MQ_HOST = 'localhost'

TEMPR_STORE_EVENTS_AND_PUSH = 0
TEMPR_STORE_AUTH_DB = 1
TEMPR_STORE_ANTI_HAMMERING_DB = 2
TEMPR_STORE_TRENDS_DB = 3
TEMPR_STORE_CONFIRMPHONE_DB = 4
TEMPR_STORE_POSTSMS_DB = 5

CERT_FILE = '/srv/Tempr/Jobs/push_dev_cert.pem'
KEY_FILE = '/srv/Tempr/Jobs/push_dev_key_noenc.pem'
USE_SANDBOX = True
