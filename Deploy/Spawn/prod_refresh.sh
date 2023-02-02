#!/bin/bash

HOSTS="gw.prod ws.prod db.prod db2.prod me.prod dashboard.prod es.prod jobs.prod store.prod"
IPS="ws 178.62.255.211 10.133.17.59 128.199.41.89  10.133.17.135 128.199.44.171 10.133.17.163 178.62.210.225 10.133.17.180 178.62.232.190 10.133.17.201 128.199.50.211 10.133.17.205 188.166.15.194 10.133.18.119 188.166.18.190 10.133.18.128 128.199.50.51 10.133.17.203"

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

function rebuild_hosts() {
	for i
	do
	    echo $i
		VM_ENV=prod VAGRANT_VAGRANTFILE=Vagrantfile.prod VAGRANT_DOTFILE_PATH=.vagrant/digital_ocean/prod vagrant rebuild $i
	done
} 

function refresh_known_hosts() {
	for i
	do
	    echo $i
	    ssh-keygen -f "/home/rodger/.ssh/known_hosts" -R $i
	done
}

function try_ssh_to() {
	for i
	do
    	echo "SSH TO $i"
    	ssh root@$i "uname -n; uptime"
	done
}

function do_all() {
	rebuild_hosts $*
	refresh_known_hosts $*
	try_ssh_to $*
}


#rebuild_hosts db.prod

#refresh_known_hosts $IPS
#refresh_known_hosts $HOSTS

#try_ssh_to $HOSTS

#do_all gw.prod