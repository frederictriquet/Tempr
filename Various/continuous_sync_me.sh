#!/bin/bash
./sync_me.sh
#find /opt/Tempr/ME/ | entr ./sync_me.sh
while inotifywait -e modify -e create -e delete -e move -rq /opt/Tempr/ME/
do
	./sync_me.sh
done
