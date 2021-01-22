# Installation
## Docker
In root directory : 
```
docker-compose -f docker/development/docker-compose-custom-node.yml build
touch local/private_config_values/private_config_base_content.conf
export DOCKER_HOST_IP=$(ip route | grep docker0 | awk '{print $9}')
# optional: convert less to css (but css is checked in)
docker-compose -f docker/docker-compose-php.yml up -d node
./in-node.sh npm install
./in-node.sh npm run build
docker-compose -f docker/docker-compose-php.yml down
# only once: composer install to build vendor/
docker-compose -f docker/development/docker-compose-php.yml up
# start the complete local dev stack
docker-compose -f docker/docker-compose.yml up -d 
```

http://127.0.0.1

## Customization 
- Theme: `themes/swisscollections`
- Module: `module/SwissCollections`
- Configuration: `local/swisscollections/swisscollections`

Configuration is based on `local/swisscollections/development`
