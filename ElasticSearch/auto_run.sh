#!/bin/bash
while f=$(inotifywait -e modify -e create -rq --format %f Work/)
do
	file="Work/$f"
    echo check $file
	if [ -f "$file" ]
	then
		echo LAUNCH $file
		chmod +x $file
		$file
	fi
	echo -e "\\033[1;32m####################################################################################################### DONE $(date +'%T')\\033[0;39m"
done