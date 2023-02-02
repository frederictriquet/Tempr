#!/bin/bash

rsync --exclude-from=/opt/Tempr/ME/.rsync-filter -F -tvaz /opt/Tempr/ME/ root@${TEMPR_HOST}:/var/www/html/ME/
echo -e "\\033[1;32m${TEMPR_HOST}\\033[0;39m"
echo -e "\\033[1;32m############################################################################################### ME SYNC DONE $(date +'%T')\\033[0;39m"
