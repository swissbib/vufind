# Installation
## Docker
```
touch local/private_config_values/private_config_base_content.conf
npm install
npm run build
composer install
export DOCKER_HOST_IP=$(ip route | grep docker0 | awk '{print $9}')
docker-compose up
```
Assumes traefik 1.7 with network `traefik_default` 

http://swisscollections.localhost

## Customization 
- Theme: `themes/swisscollections`
- Module: `module/SwissCollections`
- Configuration: `local/classic/swisscollections`

Configuration is based on `local/classic/development`
