#!/usr/bin/env python
# -*- coding: UTF-8

import inspect
import logging
import sys
import traceback


def log_init(_filename=None, _level=logging.WARNING):
    if (_filename == None):
        _filename = inspect.stack()[1][1] + '.log'
    try:
        if _filename == 'stdout':
            logging.basicConfig(format='[%(asctime)s] %(message)s',
                            level=_level)
        else:
            logging.basicConfig(format='[%(asctime)s] %(message)s',
                                filename=_filename,
                                level=_level)
    except Exception, e:
        print e

def log_error(msg):
    try:
        logging.error(msg + "\n" + traceback.format_exc())
    except Exception, e:
        print e

def log_warning(msg):
    try:
        logging.warning(msg)
    except Exception, e:
        print e

def log_info(msg):
    try:
        logging.info(msg)
    except Exception, e:
        print e

def log_debug(msg):
    try:
        logging.debug(msg)
    except Exception, e:
        print e
