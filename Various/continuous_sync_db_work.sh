#!/bin/bash
./sync_db_work.sh
while inotifywait -e modify -e create -e delete -e move -rq /opt/Tempr/Work/
do
	./sync_db_work.sh
done