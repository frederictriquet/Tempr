#!/usr/bin/python

words = open('../RawData/touslesmots.txt', 'r').read().replace("\r\n", " ").split(' ')

i = 0
for w in words:
	w = w.strip(' ')
	if len(w) > 0:
		i = i + 1
		print str(i) + '|#' + w
	if i > 10:
		break
		pass
