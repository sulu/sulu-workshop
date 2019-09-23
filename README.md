# Symfony Live Berlin 2019 Workshop

This is the repository of the Sulu workshop at the Symfony Live Berlin 2019. The repository is based on 
the [sulu/skeleton](https://github.com/sulu/skeleton) with small additions such as Bootstrap or Symfony Encore.

The assignments can be found in the folder [assignments](/assignments).

## Requirements

- PHP 7.2 or higher
- Relational Database like MySQL, MariaDB or PostgreSQL

#### Optional requirements
- [Symfony CLI Tool](https://symfony.com/doc/master/cloud/getting-started.html)
- [Docker Engine](https://docs.docker.com/engine/installation/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Getting started

If you are using docker as service provider run `docker-compose up` to start the database engine.

Else create a `.env.local` in the root directory and adapt the database credentials to your needs.

Run `composer install -o` to install all the dependencies.

Run `bin/console sulu:build dev --destroy` to initialize Sulu.

Run `bin/console server:run` to use the PHP built-in web server or run `symfony server:start --daemon` 
if you have the Symfony CLI Tools installed.
