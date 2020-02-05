#!/bin/bash
#to execute this script, you first need to establish a tunnel to one of the elasticsearch hosts (currently ues5 for example)

HOST=localhost
PORT=9201
OLD_TIMESTAMP=2019-11-22
NEW_TIMESTAMP=2020-02-03

PERSONS_INDEX=sb-persons
ORGANISATIONS_INDEX=sb-organisations
DOCUMENTS_INDEX=sb-documents
ITEMS_INDEX=sb-items
RESOURCES_INDEX=sb-resources

for index in ${PERSONS_INDEX} ${ORGANISATIONS_INDEX} ${DOCUMENTS_INDEX} ${ITEMS_INDEX} ${RESOURCES_INDEX}
do
  curl -XPOST "http://${HOST}:${PORT}/_aliases" -H "Content-Type: application/json" -d"
  {
      \"actions\" : [
          { \"add\" : { \"index\" : \"${index}-${NEW_TIMESTAMP}\", \"alias\" : \"${index}\" } },
          { \"remove\" : { \"index\" : \"${index}-${OLD_TIMESTAMP}\", \"alias\" : \"${index}\" } }
      ]
  }"
done


#the gnd index is different
#in kibana, use
#POST _aliases
#  {
#    "actions": [
#    {"add" : { "index" : "gnd20180309", "alias" : "gnd-dnb" }}
#    ]
#  }