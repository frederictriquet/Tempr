#!/bin/bash
sleep 2
rsync --exclude-from=/opt/Tempr/DB/.rsync-filter -tvaz /opt/Tempr/DB/ root@${TEMPR_HOST}:/srv/Tempr/DB/
ssh root@${TEMPR_HOST} su - postgres /srv/Tempr/DB/bin/destroy_database.sh
ssh root@${TEMPR_HOST} su - postgres /srv/Tempr/DB/bin/build_database.sh
#[ -f ./make_schema.sh ] && ./make_schema.sh
echo -e "\\033[1;32m${TEMPR_HOST}\\033[0;39m"
echo -e "\\033[1;32m############################################################################################### DB SYNC DONE $(date +'%T')\\033[0;39m"
