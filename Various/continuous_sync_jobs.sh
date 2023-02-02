#!/bin/bash
./sync_jobs.sh
#find /opt/Tempr/Jobs/ | entr ./sync_jobs.sh
while inotifywait -e modify -e create -e delete -e move -rq /opt/Tempr/Jobs/
do
	./sync_jobs.sh
done
