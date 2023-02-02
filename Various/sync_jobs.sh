#!/bin/bash

rsync --exclude-from=/opt/Tempr/Jobs/.rsync-filter -tvaz /opt/Tempr/Jobs/ root@${TEMPR_HOST}:/srv/Tempr/Jobs/
echo -e "\\033[1;32m${TEMPR_HOST}\\033[0;39m"
echo -e "\\033[1;32m############################################################################################# JOBS SYNC DONE $(date +'%T')\\033[0;39m"
