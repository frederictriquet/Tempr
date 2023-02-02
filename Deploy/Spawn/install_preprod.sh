#!/bin/bash

# $1 = local_one | preprod_one
# $2 = all | provisioning | deployment
# $3 = tags
# $4 = groups
function deploy() {
    DEST="$1"
    ACTION="$2"
    T="$3"
    G="$4"
    if [ "$G" = "" ]
    then
        G="${T}_group"
    fi
	#CONF='preprod_one'
    #CONF='local_one'
    CONF=$DEST
	OPTIONS=''
	OPTIONS="$OPTIONS -v"
	OPTIONS="$OPTIONS -i Inventories/$CONF"
	OPTIONS="$OPTIONS -u root"
	OPTIONS="$OPTIONS -l $G"
    #OPTIONS="$OPTIONS -l le_group"
    #OPTIONS="$OPTIONS --tags=core,sshd,snmpd,ufw,db,ws,me,elasticsearch,dashboard,jobs,doc,letsencrypt"
    #OPTIONS="$OPTIONS --tags=core,ufw,db,ws,dashboard,jobs,doc"
	#OPTIONS="$OPTIONS --tags=dashboard"
	OPTIONS="$OPTIONS --tags=$T"
    #OPTIONS="$OPTIONS --skip-tags=letsencrypt"
	OPTIONS="$OPTIONS --private-key=/home/rodger/.ssh/id_rsa"

	#ACTION='all'
    #ACTION='provisioning'
	#ACTION='deployment'
    echo ansible-playbook $OPTIONS --extra-vars "@generated_vars/$CONF.json" $ACTION.yml
    #ansible $OPTIONS all -m setup  # without tags/skip-tags
    #ansible $OPTIONS all -a "/sbin/ifconfig"
    #ansible-playbook $OPTIONS -e "$(< global_vars/$CONF)" --extra-vars "@generated_vars/hosts.json" $ACTION.yml
    ansible-playbook $OPTIONS --extra-vars "@generated_vars/$CONF.json" $ACTION.yml
}

function s_status() {
	ansible -i Inventories/$1 -u root all -a "/etc/init.d/redis-server status"
	ansible -i Inventories/$1 -u root all -a "/etc/init.d/rabbitmq-server status"
}

function s_stop() {
	ansible -i Inventories/$1 -u root all -a "/etc/init.d/supervisor stop"
	ansible -i Inventories/$1 -u root all -a "/etc/init.d/redis-server stop"
	ansible -i Inventories/$1 -u root all -a "/etc/init.d/rabbitmq-server stop"
}

function s_start() {
	ansible -i Inventories/$1 -u root all -a "/etc/init.d/redis-server start"
	ansible -i Inventories/$1 -u root all -a "/etc/init.d/rabbitmq-server start"
	ansible -i Inventories/$1 -u root all -a "/etc/init.d/supervisor start"
}







#deploy preprod_one provisioning jobs
#deploy preprod_one deployment ws
#s_stop preprod_one
#deploy preprod_one deployment db
#s_start preprod_one
#deploy preprod_one deployment jobs
#deploy preprod_one deployment doc

#deploy preprod_one all elasticsearch
#deploy preprod_one deployment me
#deploy preprod_one deployment store
#deploy preprod_one provisioning store
#deploy preprod_one deployment dashboard
#deploy preprod_one deployment doc

