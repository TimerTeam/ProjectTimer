#!/usr/bin/env bash

SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"
cd $SCRIPTPATH

cd ../
docker-compose up --build -d
sleep 3
docker-compose exec web php bin/console doctrine:migration:migrate
cd -
docker-compose down
