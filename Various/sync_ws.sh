#!/bin/bash

rsync --exclude-from=/opt/Tempr/WS/.rsync-filter -tvaz /opt/Tempr/WS/ root@${TEMPR_HOST}:/var/www/html/WS/
echo -e "\\033[1;32m${TEMPR_HOST}\\033[0;39m"
echo -e "\\033[1;32m############################################################################################## WEB SYNC DONE $(date +'%T')\\033[0;39m"