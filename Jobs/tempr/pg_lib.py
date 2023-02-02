#!/usr/bin/env python
# -*- coding: UTF-8

from collections import OrderedDict
from datetime import datetime
import psycopg2
from psycopg2._psycopg import ProgrammingError
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

def db_update_obj(con, table, where_fields, data):
    sql = "UPDATE %s SET " % table
    # SET PART
    set_ = ""
    sep = ""
    params = tuple()
    for k in data:
        set_ = set_ + sep + k + ' = %s '
        sep = ","
        params = params + (data[k],)
    # WHERE
    where = ""
    sep = ""
    for k in where_fields:
        where = where + sep + k + ' = %s '
        sep = " AND "
        params = params + (where_fields[k],)
    sql = sql + set_ + " where " + where
    # print sql
    # print params
    cur = con.cursor()
    cur.execute(sql, params)
    cur.close()
    con.commit()

# def db_update_obj_bad(con, table, where_fields, data):
#     sql = "UPDATE %s SET " % table
#     # SET PART
#     set_ = ""
#     sep = ""
#     t = tuple()
#     for k in data:
#         set_ = set_ + sep + k + ' = '
#         if type(data[k]) in (IntType, LongType):
#             set_ = set_ + "%s"
#             t = t + (data[k],)
#         elif type(data[k]) == datetime:
#             set_ = set_ + "'%s'"
#             t = t + (con.escape_string(str(data[k])),)
#         else:
#             set_ = set_ + "'%s'"
#             t = t + (con.escape_string(data[k].encode('utf-8')),)
#         sep = ","
#     set_ = set_ % t
#
#     # WHERE
#     where = ""
#     sep = ""
#     t = tuple()
#     for k in where_fields:
#         where = where + sep + k + ' = '
#         if type(where_fields[k]) in (IntType, LongType):
#             where = where + "%s"
#             t = t + (where_fields[k],)
#         elif type(where_fields[k]) == datetime:
#             where = where + "'%s'"
#             t = t + (con.escape_string(str(where_fields[k])),)
#         else:
#             where = where + "'%s'"
#             t = t + (con.escape_string(where_fields[k].encode('utf-8')),)
#         sep = " AND "
#     where = where % t
#     sql = sql + set_ + " where " + where
#     print sql
#     cur = con.cursor()
#     cur.execute(sql, params)
#     cur.close()
#     con.commit()


def db_execute(cur, req, params=()):
    try:
        cur.execute(req, params)
    except Exception as e:
        print str(e)
    try:
        rows = cur.fetchall()
        return rows
    except ProgrammingError:
        return None

def db_call(cur, procname, params=()):
    try:
        cur.callproc(procname, params)
    except Exception as e:
        print str(e)




