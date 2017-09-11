<p align="center"><img width="140px" src="https://cdn.rawgit.com/Bludata/base/e6da2a03/logo.png"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/Bludata/base.svg?branch=master"></a>
<a href="https://styleci.io/repos/102138603"><img src="https://styleci.io/repos/102138603/shield?branch=master" alt="StyleCI"></a>
<a href="https://styleci.io/repos/102138603"><img src="https://scrutinizer-ci.com/g/Bludata/base/badges/quality-score.png?b=master" alt="StyleCI"></a>
<a href="https://codecov.io/gh/bludata/base"><img src="https://codecov.io/gh/bludata/base/branch/master/graph/badge.svg" alt="Codecov" /></a>
</p>
<p align="center">
<a href="https://packagist.org/packages/bludata/base"><img src="https://poser.pugx.org/bludata/base/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/bludata/base"><img src="https://poser.pugx.org/bludata/base/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/bludata/base"><img src="https://poser.pugx.org/bludata/base/license.svg" alt="License"></a>
</p>

### Prerequisites

[Docker](https://docker.com)

```
$ curl -sSL https://get.docker.com/ | sh
```

### Installing

Start all containers with `docker-compose`:

```
$ docker-compose up -d
```

Wait for all containers be started and run `composer` inside container `server`:

```
$ docker-compose exec server composer install
```

## Running the tests

We use PHPUnit for unit tests, run it inside `server` container:

```
$ docker-compose exec server ./vendor/bin/phpunit
```
### And coding style tests

We use [PHP-FIR PSR's](http://www.php-fig.org/) recommendations, before send some code, please run an `php-cs-fixer` on your code and respect those standards.

## Built With

* [Laravel](https://www.laravel.com/docs/) - The PHP Framework For Web Artisans
* [Doctrine](http://www.doctrine-project.org/) - ORM/ODM

## Contributing

Please read [CONTRIBUTING.md](https://github.com/Bludata/base/blob/master/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## Authors

See also the list of [contributors](https://github.com/bludata/base/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* To make the world better, start with you
