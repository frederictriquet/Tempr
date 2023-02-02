#!/usr/bin/env python
# -*- coding: UTF-8

# TODO move this to a specific project

##################
from elasticsearch import Elasticsearch, helpers
import logging
import time

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


start = time.time()
# deactivate logging to prevent Requests from polluting my logs
# requests_log = logging.getLogger("requests")
# requests_log.addHandler(logging.NullHandler())
# requests_log.propagate = False

es = Elasticsearch([conf.TEMPR_ES_HOST])

for m in ('elasticsearch', 'elasticsearch.trace', 'requests', 'urllib3'):
    logger = logging.getLogger(m)
    logger.addHandler(logging.NullHandler())
    # logger.setLevel(logging.CRITICAL)
    logger.propagate = False

log = Log('cities', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))



# delete index: cities
# print 'DELETE'
r = es.indices.delete(index='cities', ignore=[400, 404])
# print r

# create index: cities

s = '{\
        "mappings": {\
               "cities": {\
                "properties": {\
                    "id": {"type": "integer"},\
                    "name": {"type": "string"},\
                    "country": {"type": "string"},\
                    "location": {"type": "geo_point"}\
                }\
            }\
        }\
    }'
# print 'CREATE'
# print s

r = es.indices.create('cities', s)

con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
sql = "select * from cities"  # where country='FR'"

cur = con.cursor()
actions = []
i = 0
for obj in db_retrieve_yield(con, sql):
    i = i + 1
    action = {
              "_index": "cities",
              "_type": "cities",
              "_id": obj[0],
              "_source": {
                          "name": obj[1],
                          "country": obj[4],
                          "location": {"lat":obj[2], "lon":obj[3]}
                          }
              }
    actions.append(action)
    if i % 1000 == 0:
        r = helpers.bulk(es, actions)
        # print r
        log.debug('cities ' + str(i))
        actions = []

cur.close()

if len(actions) > 0:
    r = helpers.bulk(es, actions)
# print r
# print("Total time: %.2fs" % round(time.time() - start))
