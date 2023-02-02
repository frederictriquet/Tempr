#!/bin/bash

rsync --exclude-from=/opt/Tempr/ElasticSearch/.rsync-filter -tvaz /opt/Tempr/ElasticSearch/ root@${TEMPR_HOST}:/srv/Tempr/ElasticSearch/
echo -e "\\033[1;32m${TEMPR_HOST}\\033[0;39m"
echo -e "\\033[1;32m################################################################################### ELASTIC SEARCH SYNC DONE $(date +'%T')\\033[0;39m"