ansible -i Inventories/local_multi -u root all -a "uname -a"
ansible -i Inventories/local_multi_replication -u root all -a "uname -a"
