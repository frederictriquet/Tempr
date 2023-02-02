#!/bin/bash

TARGET=$1
COMMAND=$2

function abort_if_unsure() {
	local str='ABCDEFGHIJKLMNOPQRSTUVWXYZ'
	local k=${str:$(($RANDOM%26)):1}
	local choice
	if [[ $1 != "" ]]
	then
		echo $1
	fi
	echo -n "Enter '$k' to confirm: "
	read choice
	if [[ "$k" != "$choice" ]]
	then
		echo "ABOTTING"
		exit -1
	fi
}


USAGE="USAGE: $0 <local|preprod|local_multi> <up|destroy|suspend|resume|halt>"
case "$TARGET" in
    local)
        echo DEV
        VAG_ENV='dev'
        VAG_PROVIDER='virtualbox'
        VAG_VAGRANTFILE=Vagrantfile
        CONTEXT=''
        ;;
    local_multi)
        echo DEV MULTI
        VAG_ENV='local_multi'
        VAG_PROVIDER='virtualbox'
        VAG_VAGRANTFILE=Vagrantfile
        CONTEXT='multi'
        ;;
    preprod)
        echo PREPROD
        VAG_ENV='preprod'
        VAG_PROVIDER='digital_ocean'
        VAG_VAGRANTFILE=Vagrantfile.preprod
        abort_if_unsure "Are you sure you would not prefer to 'rebuild' instead ?\nIP address will be lost and DO config may be required"
        echo "Press Ctrl+C to stop, enter to continue"
        read
        ;;
    prod)
        echo PROD
        VAG_ENV='prod'
        VAG_PROVIDER='digital_ocean'
        VAG_VAGRANTFILE=Vagrantfile.prod
        CONTEXT='prod'
        abort_if_unsure "PROD PROD PROD PROD !!!!"
        abort_if_unsure "REALLY ?"
        ;;
    *)
        echo ERROR
        echo $USAGE
        exit -1
        ;;
esac


APPEND=''
if [ "$COMMAND" = "up" -o "$COMMAND" = "rebuild" ]
then
    APPEND="--provider=$VAG_PROVIDER"
fi

VM_ENV="$VAG_ENV" VAGRANT_VAGRANTFILE="$VAG_VAGRANTFILE" VAGRANT_DOTFILE_PATH=".vagrant/$VAG_PROVIDER/$CONTEXT" vagrant $COMMAND $APPEND

if [ "$COMMAND" = "up" -a "$VAG_PROVIDER" = "digital_ocean" ]
then
    #../../Various/DigitalOcean/floating_ip.py
    #ssh-keygen -f "/home/rodger/.ssh/known_hosts" -R preprod
    echo
fi

echo "Command was:"
echo VM_ENV="$VAG_ENV" VAGRANT_VAGRANTFILE="$VAG_VAGRANTFILE" VAGRANT_DOTFILE_PATH=".vagrant/$VAG_PROVIDER/$CONTEXT" vagrant $COMMAND $APPEND

# supend / resume

# VM_ENV="preprod" VAGRANT_DOTFILE_PATH=".vagrant/virtualbox" vagrant hostmanager
