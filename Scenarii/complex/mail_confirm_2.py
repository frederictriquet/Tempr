#!/usr/bin/env python
# -*- coding: UTF-8

import urllib

if 'LIBS' not in globals():  LIBS = '../lib'

execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')

sql('select * from email_confirmations')
sql('select * from users')
