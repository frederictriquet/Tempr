#!/usr/bin/python
import random

def gen_one_name():
	return random.choice(firstnames), random.choice(lastnames)




lastnames = open('../RawData/noms.txt', 'r').read().splitlines()
firstnames = open('../RawData/prenoms.txt', 'r').read().splitlines()

for i in range(1, 50):
	login = str(i)
	fn, ln = gen_one_name()
	print login + "|" + login + "|" + fn + "|" + ln + "|pass|confirmed|"

