#!/bin/bash
./sync_doc.sh
while inotifywait -e modify -e create -e delete -e move -rq /opt/Tempr/Doc/WS/DOC/
do
	./sync_doc.sh
done
