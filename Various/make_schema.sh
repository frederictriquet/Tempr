DBHOST=172.16.100.10

ssh root@${DBHOST} ufw allow from 172.16.1.1 to ${DBHOST} port 5432

java -jar schemaSpy_5.0.0.jar -t pgsql -host ${DBHOST} -db tempr -s public -u postgres -p postgres -o output/ -dp ./postgresql-8.0-312.jdbc3.jar
