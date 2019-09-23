# Symfony Live Berlin 2019 Workshop

This is the repository of the Sulu workshop at the Symfony Live Berlin 2019. The repository is based on 
the [sulu/skeleton](https://github.com/sulu/skeleton) with small additions such as Bootstrap or Symfony Encore.

The assignments can be found in the folder [assignments](/assignments).

## Requirements

- PHP 7.3 or higher
- Relational Database like MySQL, MariaDB or PostgreSQL

#### Optional requirements
- [Symfony CLI Tool](https://symfony.com/doc/master/cloud/getting-started.html)
- [Docker Engine](https://docs.docker.com/engine/installation/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Getting started

### Setting up database

If you are using docker as service provider run the following command to start the database engine:

```bash
docker-compose up
```

Else create a `.env.local` in the root directory and adapt the database credentials to your needs:

```dotenv
DATABASE_URL=mysql://DB_USER:DB_PASSWORD@127.0.0.1:3306/DB_NAME
```

### Install the dependencies

Use [composer](https://getcomposer.org/) to install all dependencies:

```bash
composer install --optimize-autoloader
```

### Initialize Sulu Database

Run the following command to initialize Sulu:

```bash
bin/console sulu:build dev --destroy
```

### Run Webserver

Use the PHP built-in web-server with:

```bash
bin/console server:run
```

or if you have the SYMFONY CLI Tools installed run:

```bash
symfony server:start
```
