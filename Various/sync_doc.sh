ruby -ryaml -rjson -e 'puts JSON.pretty_generate(YAML::load(ARGF.read))'  < /opt/Tempr/Doc/WS/DOC/tempr.yml  > /opt/Tempr/Doc/WS/DOC/swagger.json
#scp /opt/Tempr/Doc/WS/DOC/swagger.json root@${TEMPR_HOST}:/var/www/html/DOC/

rsync --exclude-from=/opt/Tempr/Doc/WS/DOC/.rsync-filter -tvaz /opt/Tempr/Doc/WS/DOC/ root@${TEMPR_HOST}:/var/www/html/DOC/
echo -e "\\033[1;32m${TEMPR_HOST}\\033[0;39m"
echo -e "\\033[1;32m############################################################################################## DOC SYNC DONE $(date +'%T')\\033[0;39m"