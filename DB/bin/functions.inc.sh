##############################
if [ "$__FUNCTIONS_INC_SH" == "" ]
then
	__FUNCTIONS_INC_SH="DEFINED"
##############################

GREEN="\\033[1;32m"
NORMAL="\\033[0;39m"
RED="\\033[1;31m"
BLUE="\\033[1;34m"
YELLOW="\\033[1;33m"


function f_echo_color() {
	echo -e "$*${NORMAL}"
}

function f_echo_error() {
	f_echo_color $RED "$*"
}

function f_debug() {
	if [ "$DEBUG" == "YES" ]
	then
		f_echo_color ${YELLOW} "$*"
	fi
}

# arg1: return code (mandatory)
# arg2: log file (mandatory, pass /dev/null if you haven't one)
# arg3: message to print if return code != 0
function check() {
	if [ $1 != 0 ]
	then
		f_echo_error "[FAILURE] $3 (errcode=$1)"
		f_echo_error "LOG FILE ----------- BEGIN"
		cat $2
		f_echo_error "LOG FILE ------------- END"
        f_echo_error "[FAILURE] $3 (errcode=$1)"
				exit $1
	else
		if [ "$3" != "" ]
		then
			f_debug "$GREEN[SUCCESS] $3$NORMAL"
		fi
	fi
}


function ensure_not_empty() {
	if [ -z "$1" ]
	then
		f_error_error "EMPTY $2"
		exit 1
	fi
}

function ensure_number() {
	shopt -s extglob
	if [[ "$1" != +([0-9]) ]]
	then
		f_echo_error "NOT A NUMBER $2"
		exit 1
	fi
}

function ensure_ascii() {
	shopt -s extglob
	if [[ "$1" != +([0-9a-zA-Z_]) ]]
	then
		f_echo_error "NOT ASCII $2"
		exit 1
	fi
}

function ensure_directory() {
	if [ ! -d "$1" ]
	then
		f_echo_error "NOT A DIRECTORY $2"
		exit 1
	fi
}

function f_tmp_logfile() {
	if [ "$1" != "" ]
	then
		ensure_ascii "$1"
		echo "/tmp/$1.log"
	else
		echo $(mktemp /tmp/psql.XXXXXXX.log)
	fi
}

##############################
# IFDEF
fi
##############################
