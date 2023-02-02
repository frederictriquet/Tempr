#!/bin/bash
sleep 2
#rsync --exclude-from=/opt/Tempr/Work/.rsync-filter -tvaz /opt/Tempr/Work/ root@${TEMPR_HOST}:/srv/Tempr/Work/
rsync -tvaz /opt/Tempr/Work/ root@${TEMPR_HOST}:/srv/Tempr/Work/
ssh root@${TEMPR_HOST} su - postgres /srv/Tempr/DB/bin/work.sh
echo -e "\\033[1;32m${TEMPR_HOST}\\033[0;39m"
echo -e "\\033[1;32m########################################################################################## DB WORK SYNC DONE $(date +'%T')\\033[0;39m"
