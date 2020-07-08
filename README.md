# Symfony Live Berlin 2019 Workshop

This repository contains the **project template**, **assignments** and **example solution code** for the Sulu workshop 
at the Symfony Live Berlin 2019. The workshop consists of 12 assignments that guide you through creating a small 
website that integrates two simple custom entities with using Sulu content management system. 
The project builds upon the official [sulu/skeleton](https://github.com/sulu/skeleton) template and adds some project 
specific libraries such as Bootstrap or Symfony Encore.

The **assignments** of the workshop are located in the [assignments](/assignments) folder of the repository.
The **example solution code** is available per assignment as separate [repository branch](https://github.com/sulu/sulu-workshop-symfony-live-berlin-2019/branches). 
You can easily filter the changes for a single assignment by utilizing the 
[compare feature of GitHub](https://github.com/sulu/sulu-workshop-symfony-live-berlin-2019/compare/assignment/08...assignment/09).

## Requirements

- PHP 7.2 or higher
- Relational Database like MySQL, MariaDB or PostgreSQL

#### Optional requirements
- [Symfony CLI Tool](https://symfony.com/doc/master/cloud/getting-started.html)
- [Docker Engine](https://docs.docker.com/engine/installation/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Getting started

### Setting up your database

If you choose to run your services with docker you can startup your database by executing the following command:

```bash
docker-compose up
```

If you prefer to use your local database you can configure your credentials in a `.env.local` file in the root directory of the project:

```dotenv
DATABASE_URL=mysql://DB_USER:DB_PASSWORD@127.0.0.1:3306/DB_NAME
```

### Installing the dependencies

Use [composer](https://getcomposer.org/) to install the dependencies of the project:

```bash
composer install --optimize-autoloader
```

### Initialize the Sulu Database

Run the following command to initialize the database that will be used by Sulu:

```bash
bin/console sulu:build dev --destroy
```

### Run Webserver

You can startup the built-in PHP web-server with:

```bash
php -S localhost:8009 -t public config/router.php
```

If you have the SYMFONY CLI Tools installed and want to increase your performace you can also use the following command to startup the SYMFONY webserver:

```bash
symfony server:start
```

## Development

The project setup in the repository includes several development tools that help you to improve the quality of your code

### PHPUnit

The project already contains some unit tests and functional tests. They can be executed with the following commands:

```bash
# create and update test database
composer bootstrap-test-environment

# execute all test cases
composer test
```

You can can pass additional phpunit arguments by appending `-- <arguments>` to the `composer test` command.

```bash
composer test -- --stop-on-fail
```

### PHP-CS-Fixer

To keep your code consistent you can automatically reformat your code with the following command:

```bash
composer php-cs-fix
```

### PHPStan

PHPStan helps you to catch bugs before they actually occur by statically analyzing your code. Use following command to run it:

```bash
composer phpstan
```
