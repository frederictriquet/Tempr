#!/bin/bash
./sync_ws.sh
#find /opt/Tempr/WS/ | entr ./sync_ws.sh
while inotifywait -e modify -e create -e delete -e move -rq /opt/Tempr/WS/
do
	./sync_ws.sh
done
