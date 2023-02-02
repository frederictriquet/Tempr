#!/usr/bin/env python
# -*- coding: UTF-8

import logging, sys

execfile('lib/utils.py')


root = logging.getLogger()
root.setLevel(logging.DEBUG)

ch = logging.StreamHandler(sys.stdout)
ch.setLevel(logging.DEBUG)
formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s')
ch.setFormatter(formatter)
root.addHandler(ch)

sql("delete from users")
sql("select * from users")