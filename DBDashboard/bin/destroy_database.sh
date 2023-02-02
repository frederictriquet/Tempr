#!/bin/bash
DEBUG=YES
source $(dirname $0)/conf.sh
source $(dirname $0)/functions.inc.sh
source $(dirname $0)/db.inc.sh

LOGFILE=`f_tmp_logfile`

f_debug 'Log file is ' ${LOGFILE}

#echo DB_SERVER=${DB_SERVER}
echo DB_NAME=${DB_NAME}
echo DB_SCHEMA=${DB_SCHEMA}
echo DB_ROOT_LOGIN=${DB_ROOT_LOGIN}
echo DB_LOGIN=${DB_LOGIN}
echo "THIS SCRIPT WILL DESTROY:"
echo "- ${DB_NAME}"
#echo "press enter to go on or ^C to abort"
#read

rm -f ${LOGFILE}

f_debug dropdb -w --if-exists "${DB_NAME}"
dropdb -w --if-exists "${DB_NAME}" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "DROP DATABASE"

f_debug dropuser -w --if-exists "${DB_LOGIN}"
dropuser -w --if-exists "${DB_LOGIN}" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "DROP USER"

#createdb -w -U "${DB_ROOT_LOGIN}" -h "${DB_SERVER}" -p "${DB_PORT}" "${DB_NAME}" >> ${LOGFILE} 2>&1

