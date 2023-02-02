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



#deploy local_one provisioning core
#####deploy local_one provisioning db
#deploy local_one provisioning jobs
#deploy local_one provisioning store

#deploy local_one deployment me
#deploy local_one deployment doc
#deploy local_one deployment store
#deploy local_one deployment dashboard


#deploy local_one deployment ws
#s_stop local_one
#deploy local_one deployment db
#s_start local_one
#deploy local_one deployment jobs


#deploy local_multi deployment db
#deploy local_multi deployment ws
#deploy local_multi deployment jobs
#deploy local_multi deployment dashboard
#deploy local_multi deployment me


# UFW
#ansible-playbook -v -i Inventories/local_multi -u root -l dbdashboard_group --tags=ufw,dbdashboard --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml


#ansible -i Inventories/local_multi -u root all -a "ufw status"
#ansible -i Inventories/local_multi -u root all -a "uname -a"
#ansible -i Inventories/local_multi_replication -u root all -a "uname -a"

#ansible-playbook -v -i Inventories/local_one -u root --tags=core,sshd,snmpd,ufw,db,ws,me,elasticsearch,dashboard,jobs,store,doc --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_one.json all.yml
#ansible-playbook -v -i Inventories/local_one -u root --tags=ws --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_one.json deployment.yml

#ansible-playbook -v -i Inventories/local_multi -u root --tags=dbdashboard --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml

# FULL INSTALL  LOCAL MULTI (SANS REPLICATION) 
#ansible-playbook -v -i Inventories/local_multi -u root --tags=core,sshd,snmpd,ufw,db,ws,me,elasticsearch,dashboard,dbdashboard,jobs,store --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml

#ansible-playbook -v -i Inventories/local_multi -u root --tags=ufw --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=librenms --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=snmpd --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml

#ansible-playbook -v -i Inventories/local_multi -u root --tags=gw --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=core,sshd,snmpd,ufw --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=db,ws,me,elasticsearch,dashboard,jobs,store --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml

#ansible-playbook -v -i Inventories/local_multi -u root --tags=me,dashboard --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json deployment.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=ws --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json deployment.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=jobs --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json deployment.yml


#ansible-playbook -v -i Inventories/local_multi -u root --tags=db --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=me --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=elasticsearch --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=dashboard --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=store --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible-playbook -v -i Inventories/local_multi -u root --tags=core,sshd,snmpd,ufw,db,ws,me,elasticsearch,dashboard,jobs,store,doc --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml
#ansible -i Inventories/local_multi_replication -u root all -a "uname -a"
#ansible-playbook -v -i Inventories/local_multi -u root --skip-tags=letsencrypt --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json all.yml

# MISE EN PLACE DE LA REPLICATION
#ansible-playbook -v -i Inventories/local_multi_replication -u root --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/local_multi.json replication.yml

