#!/bin/bash
DEBUG=1
source $(dirname $0)/conf.sh
source $(dirname $0)/functions.inc.sh
source $(dirname $0)/db.inc.sh

LOGFILE=`f_tmp_logfile tempr`

DB_FILES='install.sql init.sql'

f_debug 'Log file is ' ${LOGFILE}

#echo DB_SERVER=${DB_SERVER}
echo DB_NAME=${DB_NAME}
echo DB_SCHEMA=${DB_SCHEMA}
echo DB_ROOT_LOGIN=${DB_ROOT_LOGIN}
echo DB_LOGIN=${DB_LOGIN}
echo "THIS SCRIPT WILL DESTROY:"
echo "- ${DB_NAME}"
echo "THEN RE-CREATE IT"
#echo "press enter to go on or ^C to abort"
#read

rm -f ${LOGFILE}
f_exec_sql_cmd_nodb "DROP DATABASE IF EXISTS ${DB_NAME};" "${DB_ROOT_LOGIN}" "${DB_SERVER}" "${DB_PORT}" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "DROP DATABASE"

f_exec_sql_cmd_nodb "DROP USER IF EXISTS ${DB_LOGIN};" "${DB_ROOT_LOGIN}" "${DB_SERVER}" "${DB_PORT}" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "DROP USER"

f_exec_sql_cmd_nodb "CREATE USER ${DB_LOGIN} WITH SUPERUSER PASSWORD '"${DB_PSWD}"';" "${DB_ROOT_LOGIN}" "${DB_SERVER}" "${DB_PORT}" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "CREATE USER"

f_exec_sql_cmd_nodb "CREATE DATABASE ${DB_NAME} WITH OWNER ${DB_LOGIN};" "${DB_ROOT_LOGIN}" "${DB_SERVER}" "${DB_PORT}" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "CREATE DATABASE"


for f in ${DB_FILES}
do
	f_exec_sql_file "${DB_DIR}/$f" "${DB_LOGIN}" "${DB_SERVER}" "${DB_PORT}" "${DB_NAME}" "${DB_SCHEMA}" "${LOGFILE}"
	check $? ${LOGFILE} "run $f"
done