#!/bin/sh

#DIR="$( pwd )"
DIR=/srv/Tempr/elasticsearch-jdbc-1.7.2.1
DIR=/srv/Tempr/elasticsearch-jdbc-2.0.0.0
bin=${DIR}/bin
lib=${DIR}/lib

echo 'Delete index'
curl -XDELETE 'http://tempr:9200/cities/'
echo

        #        "schedule" : "0 0-59 0-23 ? * *",

echo 'Create index'
curl -XPUT http://tempr:9200/cities -d '
{
        "mappings": {
            "cities": {
                "properties": {
                    "id": {"type": "integer"},
                    "name": {"type": "string"},
                    "country": {"type": "string"},
                    "location": {"type": "geo_point"}
                }
            }
        }
}'

s='{
    "type" : "jdbc",
    "jdbc" : {
        "url" : "jdbc:postgresql://tempr:5432/tempr?loglevel=0",
        "user" : "tempr",
        "password" : "vierge",
        "sql" : "select pk_city_id as _id, name, country, latitude, longitude from cities",
    	"index" : "cities",
        "type" : "cities",
        "statefile" : "cities_statefile.json"
    }
}
'
s='{
    "type" : "jdbc",
    "jdbc" : {
        "url" : "jdbc:postgresql://tempr:5432/tempr?loglevel=1",
        "user" : "tempr",
        "password" : "vierge",
        "sql" : "select pk_city_id as _id, name, country, latitude, longitude from cities",
        "index" : "cities",
        "type" : "cities"
    }
}
'


echo $s | java \
    -cp "${lib}/*" \
    -Dlog4j.configurationFile=${bin}/log4j2.xml \
    org.xbib.tools.Runner \
    org.xbib.tools.JDBCImporter
echo 'Index created'

