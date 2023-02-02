
##############################
if [ "$__CONF_INC_SH" == "" ]
then
	__CONF_INC_SH="DEFINED"
##############################


TEMPR=$(dirname $0)/../..

# TODO DEPLOY
# db parameter
#DB_SERVER=172.16.100.10
#DB_SERVER=192.168.0.64
DB_PORT=5432
#DB_ROOT_LOGIN=admin
#DB_ROOT_PSWD=vierge
DB_LOGIN=tempr
DB_PSWD=vierge
DB_NAME=tempr
DB_SCHEMA=public

DB_DIR=${TEMPR}/DB/sql


##############################
# IFDEF
fi
##############################
