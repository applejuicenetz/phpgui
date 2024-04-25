#!/usr/bin/env bash

docker build -t docker.io/applejuicenetz/phpaj:dev .

docker run -p 9999:80 -v $(pwd):/var/www/html/ \
  -d \
  --name phpgui \
  --env-file .env docker.io/applejuicenetz/phpaj:dev