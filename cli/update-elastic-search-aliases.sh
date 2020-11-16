#!/bin/bash
#to execute this script, you first need to establish a tunnel with port 9201 to one of the elasticsearch hosts (currently ues5 for example)

HOST=localhost
PORT=9201
OLD_TIMESTAMP=2020-03-17
NEW_TIMESTAMP=2020-06-26
#don't forget the timestamps for subjects which is different

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


#the subject index is different (has different timestamps and prefixes

curl -XPOST "http://${HOST}:${PORT}/_aliases" -H "Content-Type: application/json" -d"
  {
      \"actions\" : [
          { \"add\" : { \"index\" : \"gnd-subjects-2020-04-08\", \"alias\" : \"sb-subjects\" } },
          { \"remove\" : { \"index\" : \"gnd-subjects-2020-03-16\", \"alias\" : \"sb-subjects\" } }
      ]
  }"
