### Executando o projeto

    //Start the app
    docker-compose up -d --build

    //Install dependencies
    docker-compose exec app composer install

    //Create, migrate and populate the database
    docker-compose exec app php bin/console d:d:create
    docker-compose exec app php bin/console d:m:migrate
    docker-compose exec app php bin/console doctrine:fixtures:load

It's done! The project will be running at `http://localhost:8090/api/v1`
- API DOC `http://localhost:8090/api/doc`

#### + Setup for testing

    //Create, migrate and populate the database
    docker-compose exec app php bin/console --env=test d:d:create
    docker-compose exec app php bin/console --env=test d:m:migrate
    docker-compose exec app php bin/console --env=test doctrine:fixtures:load
    
    //Run tests
    docker-compose exec app php bin/phpunit --testdox
