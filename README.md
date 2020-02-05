[![Build Status](https://travis-ci.org/elliotjreed/database-anonymiser.svg?branch=master)](https://travis-ci.org/elliotjreed/database-anonymiser)

# PHP Database Anonymiser

A library to anonymise datatbase data. For example, anonymising production data for use in development environments.


## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.


### Prerequisites

This package requires:

  - PHP 7.4+
  - [Composer](https://getcomposer.org/)

### Installing

To install the required dependencies, run:

```bash
composer install
```

## Running the tests

There are two test suites defined in [phpunit.xml](phpunit.xml): SQLite and MySQL. The SQLite test suite will run an instance of in-memory SQLite for faster test-driven development and ensuring core functionality. The MYSQL test suite will require a MySQL test database to run, but does account for the slight differences in syntax between SQLite and MySQL (for example, `SHOW TABLES` works in MySQL but not SQLite) - the application itself will account for these differences automatically, but for testing purposes they must be considered independently.

To run the unit tests and code sniffer checks, run:

```bash
composer run-script test
```

To run the unit tests, run:

```bash
composer run-script phpunit
```

To run the code sniffer, run:

```bash
composer run-script phpcs
```

## Usage

### Docker

This application has a Docker image available at [hub.docker.com/r/elliotjreed/database-anonymiser](https://hub.docker.com/r/elliotjreed/database-anonymiser/).

See the _Configuration_ section below for details on how to set up the database connection and table configuration.

### Anonymising

To run the anonymisation application, run:

```bash
docker run -v $PWD/config.yml:/app/config.yml elliotjreed/database-anonymiser:latest anonymise /app/config.yml
```

Please note: the anonymiser will first check whether all of the tables and columns specified in your configuration file exists. If one or more tables or columns do not exist the application will exit and will provide a message detailing the issue(s). To validate the configuration prior to running the anonymisation application, see below.

### Validating

To validate the configuration file and ensure all specified tables and columns exists in the database, run:

```bash
docker run -v $PWD/config.yml:/app/config.yml elliotjreed/database-anonymiser:latest validate /app/config.yml
```

To add the package to a project, add the package as a dependency with Composer. In your `composer.json` file, add:

```json
"require": {
    "elliotjreed/database-anonymiser": "dev-master"
},
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:elliotjreed/database-anonymiser.git"
    }
],
```

## Configuration

The application will take either a YAML, JSON, or PHP array configuration file.

The first section should be the database connection information, taking in a PHP PDO DSN string - currently MySQL and SQLite are supported, although Postgres should work as well.

For the actual anonymisation configuration you can provide a table name as the key, then either `retain` followed by the number of rows you wish to keep, `remove` followed by the number of rows you wish to remove from the table, or `truncate` to remove all rows from a table.

You can also specify `columns` you wish to have the values replaced independently or alongside `retain` or `remove`. For each column specify the column name as the key and the value you wish to have the current value replaced with.

```text
database-connection:
  dsn: "mysql:host=localhost;dbname=mydatabasename;charset=utf8;"
  username: "databaseusername"
  password: "databasepassword"
  database: "databasename"

anonymise:
  table_a:
    truncate: true

  table_b:
    retain: 20
    columns:
      column_a: "Will be replaced with this value"

  table_c:
    remove: 30
    columns:
      column_a: "Will be replaced with this value"
      column_b: "Will be replaced with this value"
```

## Built With

  - [PHP](https://secure.php.net/)
  - [PHPUnit](https://phpunit.de/) - Unit Testing
  - [Composer](https://getcomposer.org/) - Dependency Management
