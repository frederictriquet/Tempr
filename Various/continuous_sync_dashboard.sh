#!/bin/bash
./sync_dashboard.sh
#find /opt/Tempr/WS/ | entr ./sync_dashboard.sh
while inotifywait -e modify -e create -e delete -e move -rq /opt/Tempr/Dashboard/
do
	./sync_dashboard.sh
done
