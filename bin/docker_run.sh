#!/usr/bin/env bash

docker build -t docker.io/applejuicenetz/phpaj:dev .

docker run -p 9999:80 -v $(pwd):/var/www/html/ \
 --rm \
  --name phpgui \
  --env-file .env docker.io/applejuicenetz/phpaj:dev