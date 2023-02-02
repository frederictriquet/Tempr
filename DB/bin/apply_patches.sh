#!/bin/bash
abspath="$(cd "${0%/*}" && echo $PWD)"
source $abspath/conf.sh
source $abspath/functions.inc.sh

echo [DB_AUTH]
f_upgrade_schema ${DB_AUTH_DIR} ${NDB_LOGIN} ${NDB_SERVER} ${NDB_PORT} ${NDB_AUTH_NAME} ${NDB_SCHEMA}
echo;echo;
echo [DB_NSA]
f_upgrade_schema ${DB_NSA_DIR} ${NDB_LOGIN} ${NDB_SERVER} ${NDB_PORT} ${NDB_NSA_NAME} ${NDB_NSA_SCHEMA}
echo;echo;

echo [DB_METRO]
# foreach company
OLDIFS=$IFS
IFS='|'
#psql -A -t -U${NDB_ROOT_LOGIN} -h ${NDB_SERVER} -p ${NDB_PORT} -d ${NDB_AUTH_NAME} -c "SELECT * FROM get_companies();"
f_exec_sql_cmd "SELECT * FROM get_companies();" ${NDB_LOGIN} ${NDB_SERVER} ${NDB_PORT} ${NDB_AUTH_NAME} ${NDB_SCHEMA} | while read id name schema valid
do
	$abspath/apply_patches_one_comp.sh $schema
	echo
done

IFS=$OLDIFS
