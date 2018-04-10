curl -H "content-type:application/json"  -XPUT "http://${1}:${2}/word" -d '
{
  "mappings": {
      "word":{
          "properties":{
              "id":{
                  "type":"long"
              },
              "template":{
                  "type":"long"
              },
              "type":{
                  "type":"long"
              },
              "version":{
                  "type":"long"
              },
              "isDelete":{
                  "type":"long"
              },
              "word":{
                  "type":"keyword",
                  "index":"true"
              },
              "abstract":{
                  "type":"text",
                  "index":"false"
              },
              "content":{
                  "type": "text",
                  "analyzer": "ik_max_word",
                  "search_analyzer":"ik_max_word"
              },
              "createTime":{
                  "type": "date"
              },
              "updateTime":{
                  "type": "date"
              }
          }
      }
  }
}
'

echo "\n-----------------------------------\n"