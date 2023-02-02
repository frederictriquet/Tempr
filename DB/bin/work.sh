#!/bin/bash
DEBUG=YES
source $(dirname $0)/conf.sh
source $(dirname $0)/functions.inc.sh
source $(dirname $0)/db.inc.sh

LOGFILE=`f_tmp_logfile`

DB_FILES="work.sql"
DB_DIR="${TEMPR}/Work"
f_debug 'Log file is ' ${LOGFILE}

#echo DB_SERVER=${DB_SERVER}
echo DB_NAME=${DB_NAME}
echo DB_SCHEMA=${DB_SCHEMA}
#echo DB_ROOT_LOGIN=${DB_ROOT_LOGIN}
echo DB_LOGIN=${DB_LOGIN}
echo "THIS SCRIPT WILL UPDATE:"
echo "- ${DB_NAME}"
#echo "press enter to go on or ^C to abort"
#read

rm -f ${LOGFILE}

for f in ${DB_FILES}
do
	#f_exec_sql_file "${DB_DIR}/$f" "${DB_LOGIN}" "${DB_SERVER}" "${DB_PORT}" "${DB_NAME}" "${DB_SCHEMA}" "${LOGFILE}"
	f_debug f_exec_sql_file2 "${DB_DIR}/$f" "${DB_LOGIN}" "${DB_NAME}" "${DB_SCHEMA}" "${LOGFILE}"
	f_exec_sql_file2 "${DB_DIR}/$f" "${DB_LOGIN}" "${DB_NAME}" "${DB_SCHEMA}" "${LOGFILE}"
	check $? ${LOGFILE} "EXEC SQL FILE $f"
done
cat ${LOGFILE}