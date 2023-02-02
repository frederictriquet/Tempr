#!/bin/bash
./sync_db.sh
#find /opt/Tempr/DB/ | entr ./sync_db.sh
while inotifywait -e modify -e create -e delete -e move -rq /opt/Tempr/DB/{sql,bin}
do
	./sync_db.sh
done