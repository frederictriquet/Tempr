
##############################
if [ "$__DB_INC_SH" == "" ]
then
	__DB_INC_SH="DEFINED"
##############################

source $(dirname $0)/functions.inc.sh



function f_exec_sql_file {
	f_debug "ALL PARAMS = $*"
	local DB_FILE="$1"
	local DB_LOGIN="$2"
	local DB_SERVER="$3"
	local DB_PORT="$4"
	local DB_NAME="$5"
	local DB_SCHEMA="$6"
	local LOG_FILE="$7"
	#shift 7
	ensure_number "$DB_PORT" DB_PORT

	#echo DB_FILE=${DB_FILE}
	#echo DB_LOGIN=${DB_LOGIN}
	#echo DB_SERVER=${DB_SERVER}
	#echo DB_NAME=${DB_NAME}
	#echo DB_SCHEMA=${DB_SCHEMA}
	#echo LOG_FILE=${LOG_FILE}
	f_debug $PWD
	f_debug "psql --single-transaction --set ON_ERROR_STOP=on -a -U${DB_LOGIN} -h ${DB_SERVER} -p ${DB_PORT} -d ${DB_NAME} -v schema_name=${DB_SCHEMA} -v db_owner=${DB_LOGIN} -f ${DB_FILE}"
	PGOPTIONS="-c search_path=${DB_SCHEMA}" psql --single-transaction --set ON_ERROR_STOP=on -a -U${DB_LOGIN} -h ${DB_SERVER} -p ${DB_PORT} -d ${DB_NAME} -v schema_name=${DB_SCHEMA} -v db_owner=${DB_LOGIN} -f ${DB_FILE} >> ${LOG_FILE} 2>&1
	local RES=$?
	f_debug "psql return code = " $RES
	return $RES
}

function f_exec_sql_file2 {
	f_debug "ALL PARAMS = $*"
	local DB_FILE="$1"
	local DB_LOGIN="$2"
	local DB_NAME="$3"
	local DB_SCHEMA="$4"
	local LOG_FILE="$5"
	#shift 5
	ensure_number "$DB_PORT" DB_PORT

	#echo DB_FILE=${DB_FILE}
	#echo DB_LOGIN=${DB_LOGIN}
	#echo DB_NAME=${DB_NAME}
	#echo DB_SCHEMA=${DB_SCHEMA}
	#echo LOG_FILE=${LOG_FILE}
	f_debug $PWD
	f_debug "psql --single-transaction --set ON_ERROR_STOP=on -a -U${DB_LOGIN} -d ${DB_NAME} -v schema_name=${DB_SCHEMA} -v db_owner=${DB_LOGIN} -f ${DB_FILE}"
	PGOPTIONS="-c search_path=${DB_SCHEMA}" psql --single-transaction --set ON_ERROR_STOP=on -a -U${DB_LOGIN} -d ${DB_NAME} -v schema_name=${DB_SCHEMA} -v db_owner=${DB_LOGIN} -f ${DB_FILE} >> ${LOG_FILE} 2>&1
	local RES=$?
	f_debug "psql return code = " $RES
	return $RES
}

function f_exec_sql_cmd {
	f_debug "ALL PARAMS = $*"
	local SQL="$1"
	local DB_LOGIN="$2"
	local DB_SERVER="$3"
	local DB_PORT="$4"
	local DB_NAME="$5"
	local DB_SCHEMA="$6"

	ensure_number "$DB_PORT" DB_PORT
	#echo SQL=${SQL}
	#echo DB_LOGIN=${DB_LOGIN}
	#echo DB_SERVER=${DB_SERVER}
	#echo DB_NAME=${DB_NAME}
	#echo DB_SCHEMA=${DB_SCHEMA}
	#f_debug "psql -a -U${DB_LOGIN} -h ${DB_SERVER} -p ${DB_PORT} -d ${DB_NAME} -v schema_name=${DB_SCHEMA} -v db_owner=${DB_LOGIN} -c \"${SQL}\""
	PGOPTIONS="-c search_path=${DB_SCHEMA}" psql -q -A -F'|' -t -U${DB_LOGIN} -h ${DB_SERVER} -p ${DB_PORT} -d ${DB_NAME} -v schema_name=${DB_SCHEMA} -v db_owner=${DB_LOGIN} -c "${SQL}"
}

function f_exec_sql_cmd_nodb {
	f_debug "ALL PARAMS = $*"
	local SQL="$1"
	local DB_SERVER="$2"
	local DB_PORT="$3"

	ensure_number "$DB_PORT" DB_PORT
	#echo SQL=${SQL}
	#echo DB_SERVER=${DB_SERVER}
	f_debug "psql -q -a -h ${DB_SERVER} -p ${DB_PORT} -c \"${SQL}\"" template1
	psql -q -a -h ${DB_SERVER} -p ${DB_PORT} -c "${SQL}" template1
}

function f_check_patch_applicability() {
	local VAR_TYPE="$1" # int, string
	local VAR_NAME="$2"
	local EXPECTED_VALUE="$3"
	shift 3
	local DB_LOGIN="$1"
	local DB_SERVER="$2"
	local DB_PORT="$3"
	local DB_NAME="$4"
	local DB_SCHEMA="$5"

	CURRENT_DB_VALUE=`meta_get_${VAR_TYPE} "${VAR_NAME}" ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}`
	#echo DEBUG "${CURRENT_DB_VALUE}" "${EXPECTED_VALUE}" 1>&2
	if [ "${CURRENT_DB_VALUE}" == "${EXPECTED_VALUE}" ]
	then
		return 0
	else
		return 1
	fi
}

function f_upgrade_schema() {
	local BASE_PATH="$1"
	shift 1
	local DB_LOGIN="$1"
	local DB_SERVER="$2"
	local DB_PORT="$3"
	local DB_NAME="$4"
	local DB_SCHEMA="$5"

	#echo PWD $PWD ${BASE_PATH}/patches/* 1>&2
	for i in ${BASE_PATH}/patches/*
	do
		if [ ! -d "$i" ]
		then
			continue
		fi
		echo '-------- Patch' $i
		# LOAD PATCH INFO
		unset DESCRIPTION
		unset DB_TYPE
		unset APPLIES_ON_MAJOR
		unset APPLIES_ON_MINOR
		unset RAISES_TO_MINOR
		unset SQL_FILE
		source $i/patch.sh
		ensure_not_empty "$DESCRIPTION" DESCRIPTION
		ensure_not_empty "$DB_TYPE" DB_TYPE
		ensure_number "$APPLIES_ON_MAJOR" APPLIES_ON_MAJOR
		ensure_number "$APPLIES_ON_MINOR" APPLIES_ON_MINOR
		ensure_number "$RAISES_TO_MINOR" RAISES_TO_MINOR
		ensure_not_empty "$SQL_FILE" SQL_FILE

		# ENSURE PATCH CONTEXT MATCHES DB STATE
		if ! f_check_patch_applicability string db_type "${DB_TYPE}" ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
		then
			echo "ERROR: Patch not applied because of wrong db_type"
			continue
		fi
		if ! f_check_patch_applicability int version_major "${APPLIES_ON_MAJOR}" ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
		then
			echo "ERROR: Patch not applied because of wrong version_major"
			continue
		fi
		if ! f_check_patch_applicability int version_minor "${APPLIES_ON_MINOR}" ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
		then
			echo "ERROR: Patch not applied because of wrong version_minor"
			continue
		fi

		echo '    Description:' ${DESCRIPTION}
		# APPLY THE PATCH
		f_debug APPLYING SQL PATCH \"${SQL_FILE}\"
		LOG_FILE=`f_tmp_logfile`
		f_exec_sql_file $i/${SQL_FILE} ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA} ${LOG_FILE}
		RES=$?

		# ON SUCCESS, UPDATE DB STATE ACCORDING TO PATCH DEFINITION
		if [ ${RES} -eq 0 ]
		then
			f_debug SUCCESS
			meta_set_int "version_minor" ${RAISES_TO_MINOR} ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
			meta_set_ts "last_upgrade" 'now()' ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}
			f_debug DB_VERSION raised to ${APPLIES_ON_MAJOR}.${RAISES_TO_MINOR}
		else
			echo FAILURE ${RES}
			cat ${LOG_FILE}
			break
		fi
	done
	DB_VERSION_MAJOR=`meta_get_int "version_major" ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}`
	DB_VERSION_MINOR=`meta_get_int "version_minor" ${DB_LOGIN} ${DB_SERVER} ${DB_PORT} ${DB_NAME} ${DB_SCHEMA}`
	echo "-------- JOB DONE, DATABASE ${DB_NAME}/${DB_SCHEMA} NOW IN VERSION ${DB_VERSION_MAJOR}.${DB_VERSION_MINOR}"
}

function f_upgrade_version() {
	local MAJOR="$1"
	local MINOR="$2"
	local schema_name="$7"
	echo "Set DB version to ${MAJOR}.${MINOR} in schema \"$schema_name\""
	shift 2
	local CURRENT_MAJOR=$(meta_get_int "version_major" $*)
	local CURRENT_MINOR=$(meta_get_int "version_minor" $*)
	if [ $MAJOR -lt $CURRENT_MAJOR ]
	then
		echo "ERROR: current version is greater than ${MAJOR}.${MINOR}"
		return
	elif [ $MAJOR -eq $CURRENT_MAJOR ]
	then
		if [ $MINOR -lt $CURRENT_MINOR ]
		then
			echo "ERROR: current version is greater than ${MAJOR}.${MINOR}"
			return
		fi
	fi

	meta_set_int 'version_major' $MAJOR $*
	meta_set_int 'version_minor' $MINOR $*
}

function meta_get_string() {
	local NAME="$1"
	shift 1
	f_exec_sql_cmd "SELECT string_val FROM meta WHERE name='${NAME}'" "$1" "$2" "$3" "$4" "$5"
}

function meta_set_string() {
	local NAME="$1"
	local VALUE="$2"
	shift 2
	f_exec_sql_cmd "UPDATE meta SET string_val='${VALUE}' WHERE name='${NAME}'" "$1" "$2" "$3" "$4" "$5"
}

function meta_get_int() {
	local NAME="$1"
	shift 1
	f_exec_sql_cmd "SELECT int_val FROM meta WHERE name='${NAME}'" "$1" "$2" "$3" "$4" "$5"
}

function meta_set_int() {
	local NAME="$1"
	local VALUE="$2"
	shift 2
	f_exec_sql_cmd "UPDATE meta SET int_val='${VALUE}' WHERE name='${NAME}'" "$1" "$2" "$3" "$4" "$5"
}

function meta_get_ts() {
	local NAME="$1"
	shift 1
	f_exec_sql_cmd "SELECT date_val FROM meta WHERE name='${NAME}'" "$1" "$2" "$3" "$4" "$5"
}

function meta_set_ts() {
	local NAME="$1"
	local VALUE="$2"
	shift 2
	f_exec_sql_cmd "UPDATE meta SET date_val='${VALUE}' WHERE name='${NAME}'" "$1" "$2" "$3" "$4" "$5"
}




##############################
# IFDEF
fi
##############################
