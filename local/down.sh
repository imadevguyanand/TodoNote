#!/bin/bash 

#Configuration
source local/conf.sh

export APP_NAME=todo-note

docker stack rm $APP_NAME

if [[ 0 -ne $(docker secret ls -q -f name=todo-note-secrets-env | wc -l) ]]
then
  docker secret rm todo-note-secrets-env
fi