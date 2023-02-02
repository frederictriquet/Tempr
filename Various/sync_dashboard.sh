#!/bin/bash

rsync --exclude-from=/opt/Tempr/Dashboard/.rsync-filter -tvaz /opt/Tempr/Dashboard/ root@${TEMPR_HOST}:/var/www/html/Dashboard/
echo -e "\\033[1;32m${TEMPR_HOST}\\033[0;39m"
echo -e "\\033[1;32m######################################################################################## DASHBOARD SYNC DONE $(date +'%T')\\033[0;39m"