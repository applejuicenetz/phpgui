#!/bin/bash

set -e

docker buildx create --platform linux/arm/v7,linux/arm64,linux/386,linux/amd64 --name phpaj
docker buildx use phpaj

VERSION=$1
IMAGE="red171/ajgui-php"

docker buildx build \
  --build-arg BUILD_DATE=$(date -u +"%Y-%m-%dT%H:%M:%SZ") \
  --build-arg VCS_REF=$(git rev-parse --short HEAD) \
  --build-arg BUILD_VERSION=${VERSION} \
  --platform linux/arm/v7,linux/arm64,linux/386,linux/amd64 \
  --no-cache \
  -t ${IMAGE}:${VERSION} \
  -t ${IMAGE}:latest . \
  --push

docker buildx rm phpaj
