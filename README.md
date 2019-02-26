# Installation
```
docker-compose up -d

docker-compose exec php-fpm sh -c "composer install && bin/console doctrine:migrations:migrate"
```

Use passphrase eeef26ad5d5a8815975c2fbfadddb434 for generating key:
```
docker-compose exec php-fpm sh -c "mkdir config/jwt && openssl genrsa -out config/jwt/private.pem -aes256 4096 && openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem"
```

Check http://localhost:12345/ should see response "alive"

# Run tests
```
docker-compose exec php-fpm sh -c "bin/phpunit"
```

# Task

Write REST API for ToDo application

Authorized user can create, read, update, remove his todo task

There are 2 entities Task and User

User
  - id: int
  - username: string
  - password: string
  
Task
  - id: int
  - content: string
  - created_at: datetime
  - completed: bool
  - user: User
  
Use JWT token for authentication

All methods should be covered by tests

DB for your choice

Follow Symfony code style

Be ready to show demo (10-15min)

Optional:
  - API documentation
  - Docker for env
  
Toolset

Framework - Symfony 3.4 or 4

JWT - LexikJWTAuthenticationBundle - https://github.com/lexik/LexikJWTAuthenticationBundle

REST API - FOSRestBundle - https://symfony.com/doc/master/bundles/FOSRestBundle/1-setting_up_the_bundle.html

Test - PHPUnit

Documentation - NelmioApiDocBundle - https://symfony.com/doc/master/bundles/NelmioApiDocBundle/index.html
