#!/usr/bin/env python
# -*- coding: UTF-8

from collections import OrderedDict
from datetime import datetime
import psycopg2
from types import IntType, LongType


def db_connect(host, db, login, password=None):
    con = psycopg2.connect(database=db, user=login, password=password, host=host)
    return con

def db_retrieve(con, sql, params=()):
    cur = con.cursor()
    cur.execute(sql, params)
    rows = cur.fetchall()
    return rows

def db_retrieve_yield(con, sql, params=()):
    cur = con.cursor()
    cur.execute(sql, params)
    for row in cur:
        yield row

def db_reorg(pk, rows):
    res = OrderedDict()
    for row in rows:
        res[ row[pk] ] = row
    return res

def db_insert_obj(con, table, obj):
    sql = "INSERT INTO %s (" % table
    sep = ""
    values = ""
    t = tuple()
    for k in obj:
        sql = sql + sep + k
        values = values + sep
        # print k + ' ' + str(type(obj[k]))
        if type(obj[k]) in (IntType, LongType):
            values = values + "%s"
            t = t + (obj[k],)
        elif type(obj[k]) == datetime:
            values = values + "'%s'"
            t = t + (con.escape_string(str(obj[k])),)
        else:
            values = values + "'%s'"
            t = t + (con.escape_string(obj[k].encode('utf-8')),)
        sep = ","
    sql = sql + ") VALUES (" + values + ")"
    sql = sql % t
    cur = con.cursor()
    cur.execute(sql)
    cur.close()
    con.commit()

