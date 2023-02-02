#!/bin/bash
./sync_elastic.sh
while inotifywait -e modify -e create -e delete -e move -rq /opt/Tempr/ElasticSearch/
do
	./sync_elastic.sh
done
