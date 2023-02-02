#!/bin/bash
source $(dirname $0)/conf.sh
source $(dirname $0)/functions.inc.sh
source $(dirname $0)/db.inc.sh

function f_dump_var() {
    local VAR_TYPE="$1" # int, string
    local VAR_NAME="$2"
    shift 2
	local DB_LOGIN="$1"
	local DB_SERVER="$2"
	local DB_PORT="$3"
	local DB_NAME="$4"
	local DB_SCHEMA="$5"
    echo " - ${VAR_NAME} (${VAR_TYPE}) =" `meta_get_${VAR_TYPE} "${VAR_NAME}" ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}`
}

function show_db_status() {
	local DB_LOGIN="$1"
	local DB_SERVER="$2"
	local DB_PORT="$3"
	local DB_NAME="$4"
	local DB_SCHEMA="$5"

    echo ${DB_NAME}:${DB_SCHEMA} on ${DB_SERVER}:${DB_PORT}
	f_dump_var string db_type ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
	f_dump_var int version_major ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
	f_dump_var int version_minor ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
	f_dump_var ts last_upgrade ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}

}

echo '[TEMPR]'
show_db_status ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
echo
