#!/bin/sh

#DIR="$( pwd )"
DIR=/srv/Tempr/elasticsearch-jdbc-1.7.2.1
bin=${DIR}/bin
lib=${DIR}/lib

echo 'Delete index'
curl -XDELETE 'http://localhost:9200/users/'
echo

        #        "schedule" : "0 0-59 0-23 ? * *",
echo 'Create index'
s='{
    "type" : "jdbc",
    "jdbc" : {
        "url" : "jdbc:postgresql://localhost:5432/tempr?loglevel=0",
        "user" : "tempr",
        "password" : "vierge",
        "sql" : "select pk_user_id as _id, lastname as my_fullname from users",
    	"index" : "users",
        "type" : "my_names",
        "statefile" : "users_statefile.json"
    },
    "settings": {
        "analysis": {
            "analyzer": {
                "whitespace_analyzer": {
                    "type": "custom",
                    "tokenizer": "whitespace",
                    "filter": [
                        "uppercase",
                        "asciifolding"
                    ]
                }
            }
        }
    },
    "mappings": {
        "my_names": {
            "_all": {
                "index_analyzer": "whitespace_analyzer",
                "search_analyzer": "whitespace_analyzer"
            },
            "properties": {
                "fullname": {
                    "type": "string"
                }
            }
        }
    }
}
'

s='{
    "type" : "jdbc",
    "jdbc" : {
        "url" : "jdbc:postgresql://localhost:5432/tempr?loglevel=0",
        "user" : "tempr",
        "password" : "vierge",
        "sql" : "select pk_user_id as _id, lastname as my_fullname from users",
        "index" : "users",
        "type" : "my_names",
        "statefile" : "users_statefile.json"
    },
    "settings": {
        "analysis": {
            "analyzer": {
                "whitespace_analyzer": {
                    "type": "custom",
                    "tokenizer": "whitespace",
                    "filter": [
                        "uppercase",
                        "asciifolding"
                    ]
                }
            }
        }
    }
}
'


echo $s | java \
    -cp "${lib}/*" \
    -Dlog4j.configurationFile=${bin}/log4j2.xml \
    org.xbib.tools.Runner \
    org.xbib.tools.JDBCImporter
echo 'Index created'

# http://qbox.io/blog/multi-field-partial-word-autocomplete-in-elasticsearch-using-ngrams