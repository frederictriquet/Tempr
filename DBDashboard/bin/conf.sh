
##############################
if [ "$__CONF_INC_SH" == "" ]
then
	__CONF_INC_SH="DEFINED"
##############################


TEMPR=$(dirname $0)/../..

# db parameter
#DB_SERVER=172.16.1.10
DB_PORT=5432
DB_LOGIN=dbdash
DB_PSWD=vierge
DB_NAME=dbdash
DB_SCHEMA=public

DB_DIR=${TEMPR}/DBDashboard/sql


##############################
# IFDEF
fi
##############################
