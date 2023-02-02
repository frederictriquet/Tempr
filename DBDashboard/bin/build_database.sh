#!/bin/bash
DEBUG=YES
source $(dirname $0)/conf.sh
source $(dirname $0)/functions.inc.sh
source $(dirname $0)/db.inc.sh

LOGFILE=`f_tmp_logfile`

DB_FILES="install.sql"

# Obsolete files: demo_data.sql

f_debug 'Log file is ' ${LOGFILE}

#echo DB_SERVER=${DB_SERVER}
echo DB_NAME=${DB_NAME}
echo DB_SCHEMA=${DB_SCHEMA}
#echo DB_ROOT_LOGIN=${DB_ROOT_LOGIN}
echo DB_LOGIN=${DB_LOGIN}
echo "THIS SCRIPT WILL CREATE:"
echo "- ${DB_NAME}"
#echo "press enter to go on or ^C to abort"
#read

rm -f ${LOGFILE}

f_debug createuser -w "${DB_LOGIN}"
createuser -w "${DB_LOGIN}" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "CREATE USER"

#f_exec_sql_cmd_nodb "ALTER ROLE ${DB_LOGIN} WITH SUPERUSER PASSWORD '"${DB_PSWD}"';" "${DB_SERVER}" "${DB_PORT}" >> ${LOGFILE} 2>&1
f_debug psql -a -c "ALTER ROLE ${DB_LOGIN} WITH SUPERUSER PASSWORD '"${DB_PSWD}"';"
psql -a -c "ALTER ROLE ${DB_LOGIN} WITH SUPERUSER PASSWORD '"${DB_PSWD}"';" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "ALTER ROLE"

f_debug createdb -w -O "${DB_LOGIN}" "${DB_NAME}"
createdb -w -O "${DB_LOGIN}" "${DB_NAME}" >> ${LOGFILE} 2>&1
check $? ${LOGFILE} "CREATE DATABASE"


for f in ${DB_FILES}
do
	#f_exec_sql_file "${DB_DIR}/$f" "${DB_LOGIN}" "${DB_SERVER}" "${DB_PORT}" "${DB_NAME}" "${DB_SCHEMA}" "${LOGFILE}"
	f_debug f_exec_sql_file2 "${DB_DIR}/$f" "${DB_LOGIN}" "${DB_NAME}" "${DB_SCHEMA}" "${LOGFILE}"
	f_exec_sql_file2 "${DB_DIR}/$f" "${DB_LOGIN}" "${DB_NAME}" "${DB_SCHEMA}" "${LOGFILE}"
	check $? ${LOGFILE} "EXEC SQL FILE $f"
done