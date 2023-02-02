#!/usr/bin/env python
# -*- coding: UTF-8

import inspect
import logging, logging.handlers
import sys
import traceback

# DEBUG INFO WARNING ERROR CRITICAL
ll = { logging.DEBUG: 'debug', logging.INFO: 'info', logging.WARNING: 'warning', logging.ERROR: 'error', logging.CRITICAL: 'critical'}
class Log:
    def __init__(self, logger_name, path, global_level=logging.ERROR, levels=(logging.ERROR,)):
        self.logger = logging.getLogger(logger_name)
        formatter = logging.Formatter('[%(asctime)s] %(message)s')
        for l in levels:
            loggerFilename = path + '/' + logger_name + '.' + ll[l] + '.log' 
            fileHandler = logging.handlers.WatchedFileHandler(loggerFilename)
            fileHandler.setLevel(l)
            fileHandler.setFormatter(formatter)
            self.logger.addHandler(fileHandler)
        self.logger.setLevel(global_level)

    def debug(self, msg):
        self.logger.debug(msg)

    def info(self, msg):
        self.logger.info(msg)

    def warning(self, msg):
        self.logger.warning(msg)

    def error(self, msg):
        self.logger.error(msg)

    def critical(self, msg):
        self.logger.critical(msg)


if '__main__' == __name__:
    l1 = Log('test1', '/mnt/ramdisk/logs', global_level=logging.DEBUG, levels=(logging.INFO, logging.ERROR))
    l2 = Log('test2', '/mnt/ramdisk/logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.CRITICAL))

    l1.debug('1 debug1')
    l2.debug('2 debug2')
    l1.debug('1 debug3')
    l1.info('1 info1')
    l2.info('2 info2')
    l1.info('1 info3')
    l1.warning('1 warning1')
    l2.warning('2 warning2')
    import os
    os.rename('/mnt/ramdisk/logs/test1.info.log', '/mnt/ramdisk/logs/test1.info.log-old')
    l1.warning('1 warning3')
    l1.error('1 error1')
    l2.error('2 error2')
    l1.error('1 error3')
    l1.critical('1 critical1')
    l2.critical('2 critical2')
    l1.critical('1 critical3')
