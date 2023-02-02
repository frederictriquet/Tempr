#!/usr/bin/python

import Image
import ImageDraw
import StringIO
import csv, sys

# print('START')
k = 10
geofile = '/home/rodger/Geonames/cities1000.txt'
geofile = '/home/rodger/Geonames/allCountries.txt'
#geofile = '/home/rodger/Geonames/a.txt'
im = Image.new("RGB", (400 * k, 200 * k), "white")

draw = ImageDraw.Draw(im)

# draw.rectangle(((100, 100), (130, 200)), fill="green")

xmin, xmax, ymin, ymax = 0, 0, 0, 0
with open(geofile) as f:
    for line in f:
        r = line.replace('|','-').split('\t')
        # print '**', r
        if r[6] == 'P':
        # print r[0], r[1], r[4], r[5]
            x = 200 * k + float(r[5]) * k
            y = 100 * k - float(r[4]) * k
            draw.point((x, y), fill="red")

            #print r[0] + '|' + r[1] + '|' + r[4] + '|' + r[5] + '|' + r[8]
            r[3] = ''
            print '|'.join(r),


del draw

im.save("geo.png", "PNG")

# print('DONE')


sys.exit(0)
with open(geofile) as f:
    for line in f:
        a = StringIO.StringIO(line)
        reader = csv.reader(a, delimiter='\t')
        print('----')
        for r in reader:
            print '**', r
            print r[0], r[1], r[4], r[5]
            x = 200 + int(r[5])
            y = 100 - int(r[4])
            draw.point((x, y), fill="red")
