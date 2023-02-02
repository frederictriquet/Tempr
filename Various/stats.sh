#!/bin/bash
cd /mnt/ramdisk
rm -f ip.*.log ips.log
cat ws.access.log.14 ws.access.log.13 ws.access.log.12 ws.access.log.11 ws.access.log.10 ws.access.log.9 ws.access.log.8 ws.access.log.7 ws.access.log.6 ws.access.log.5 ws.access.log.4 ws.access.log.3 ws.access.log.2 ws.access.log.1 ws.access.log > all.log
#head all.log | sed 's/\(.*\) \- \- \[\(.*\)\(\ \+.*\].*\)/\1 \2/g' #| while read IP 
cat all.log | sed 's/\(.*\) \- \- \[\([^:]*\)\(.*\)/\1 \2/g' | while read IP DATE
do
     echo $DATE >> ip.$IP.log
     echo $IP >> ip.tmp
done

sort -u < ip.tmp > ips.log
rm ip.tmp


wc -l ip.188.165.96.106.log