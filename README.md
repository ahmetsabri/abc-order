# ABC Orders
## _Simple app to apply symfony_

## Installation

- clone the repo 
- Run `composer install`
- Run `php bin/console lexik:jwt:generate-keypair`
- Start serving `symfony server:start`

# OR WITH DOCKER 
- Run `docker-compose up --build`
- Create the database via 'http://localhost:8585' where server **mysql** and empty password
- Enter to the PHP doceker composer via `docker exec -it <php_container_name> sh`
- Run `composer install`
- Then `php bin/console lexik:jwt:generate-keypair`
- Finally `php bin/console doctrine:schema:create`
