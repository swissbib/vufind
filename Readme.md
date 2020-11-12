# Installation
## Docker
```
docker-compose -f docker/development/docker-compose-custom-node.yml build
touch local/private_config_values/private_config_base_content.conf
export DOCKER_HOST_IP=$(ip route | grep docker0 | awk '{print $9}')
docker-compose up -d node
./in-node.sh npm install
./in-node.sh npm run build
docker-compose down
# composer install
docker-compose -f docker/development/docker-compose-php.yml up
docker-compose up -d 
```

http://127.0.0.1

## Customization 
- Theme: `themes/swisscollections`
- Module: `module/SwissCollections`
- Configuration: `local/classic/swisscollections`

Configuration is based on `local/classic/development`
