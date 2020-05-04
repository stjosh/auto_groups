#!/bin/bash
set -e

docker build --build-arg DATE=$(date +%F) -t auto-groups-test ./tests/Docker

echo "Starting Development Server..."
ID=`docker run -d --rm -p 8080:8080 -v $(pwd):/server/apps/auto_groups auto-groups-test`

trap finish INT
function finish() {
    echo "Finishing up..."
    docker kill $ID
    echo "Finished."
}
echo "Development server started."
docker logs -f $ID


