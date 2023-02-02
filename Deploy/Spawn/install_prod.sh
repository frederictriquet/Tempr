#!/bin/bash

#ansible -i Inventories/prod -u root all -a "ufw status"
#ansible -i Inventories/prod -u root all -a "uname -a"
#ansible -i Inventories/prod_replication -u root all -a "uname -a"

#ansible-playbook -v -i Inventories/prod -u root --tags=core,sshd --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json all.yml
#ansible-playbook -v -i Inventories/prod -u root --tags=snmpd,ufw --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json all.yml
#ansible-playbook -v -i Inventories/prod -u root --tags=db,ws,me,elasticsearch,dashboard,jobs,store --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json all.yml

#ansible-playbook -v -i Inventories/prod -u root --tags=ufw --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json provisioning.yml

# JOBS
#ansible-playbook -v -i Inventories/prod -u root --tags=jobs --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json deployment.yml

# WS
#ansible-playbook -v -i Inventories/prod -u root --tags=ws --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json deployment.yml

# WWW.TEMPR.ME
#ansible-playbook -v -i Inventories/prod -u root --tags=me --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json deployment.yml

# activer les regles de FW sur DB2
#ansible-playbook -v -i Inventories/prod_replication -u root -l dbslave_group --tags=ufw --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json replication.yml

# UFW
#ansible-playbook -v -i Inventories/prod -u root -l dbdashboard_group --tags=ufw,dbdashboard --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json all.yml


# FULL INSTALL  LOCAL MULTI (SANS REPLICATION) 
#ansible-playbook -v -i Inventories/prod -u root --tags=core,sshd,snmpd,ufw,db,ws,me,elasticsearch,dashboard,jobs,store --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json all.yml



#ansible-playbook -v -i Inventories/prod -u root --tags=ws,me --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json all.yml
#ansible-playbook -v -i Inventories/prod -u root --tags=me --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json deployment.yml

# DASHBOARD
ansible-playbook -v -i Inventories/prod -u root --tags=dashboard --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json deployment.yml
#ansible-playbook -v -i Inventories/prod -u root --tags=dbdashboard --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json deployment.yml

#ansible-playbook -v -i Inventories/prod -u root --tags=snmpd,ufw,librenms --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json all.yml
#ansible-playbook -v -i Inventories/prod_replication -u root --tags=snmpd,ufw --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json all.yml


# MISE EN PLACE DE LA REPLICATION
#ansible-playbook -v -i Inventories/prod_replication -u root --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json replication.yml

# LETSENCRYPT
#ansible-playbook -v -i Inventories/prod -u root --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json letsencrypt.yml

# SECU
ansible-playbook -v -i Inventories/prod -u root --private-key=/home/rodger/.ssh/id_rsa --extra-vars @generated_vars/prod.json secu.yml
