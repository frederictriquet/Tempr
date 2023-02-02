#!/bin/bash

curl -XGET 'http://172.16.1.10:9200/rodger/sometype/_search' -d '{
    "query": {
        "bool": {
            "must": [
                {
                    "prefix": {
                        "sometype.tag": "b"
                    }
                }
            ],
            "must_not": [ ],
            "should": [ ]
        }
    },
    "from": 0,
    "size": 10,
    "sort": [ ],
    "facets": { }

}'