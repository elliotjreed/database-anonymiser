[![Build Status](https://travis-ci.org/elliotjreed/database-anonymiser.svg?branch=master)](https://travis-ci.org/elliotjreed/database-anonymiser)

# PHP Database Anonymiser

A library to anonymise datatbase data. For example, anonymising production data for use in development environments.


## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.


### Prerequisites

This package requires:
 - PHP 7.1+
 - [Composer](https://getcomposer.org/)


### Installing

To install the required dependencies, run:

```bash
composer install
```


## Running the tests

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


## Deployment

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


## Built With

* [PHP](https://secure.php.net/)
* [PHPUnit](https://phpunit.de/) - Unit Testing
* [Composer](https://getcomposer.org/) - Dependency Management
