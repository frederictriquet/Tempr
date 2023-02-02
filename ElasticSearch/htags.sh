#!/bin/sh

#DIR="$( pwd )"
DIR=/srv/Tempr/elasticsearch-jdbc-1.7.2.1
bin=${DIR}/bin
lib=${DIR}/lib

echo 'Delete index'
curl -XDELETE 'http://localhost:9200/htags/'

echo 'Create index'
echo '{
    "type" : "jdbc",
    "jdbc" : {
        "url" : "jdbc:postgresql://localhost:5432/tempr?loglevel=0",
        "user" : "tempr",
        "password" : "vierge",
        "sql" : "select pk_htag_id as _id, tag as my_tag from htags",
    	"index" : "htags",
        "type" : "htags"
    }
}
' | java \
    -cp "${lib}/*" \
    -Dlog4j.configurationFile=${bin}/log4j2.xml \
    org.xbib.tools.Runner \
    org.xbib.tools.JDBCImporter
echo 'Index created'
