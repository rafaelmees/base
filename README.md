# Base API

[![StyleCI](https://styleci.io/repos/56002039/shield)](https://styleci.io/repos/56002039)
[![Build Status](https://travis-ci.org/raivieira/base-api-php.svg?branch=master)](https://travis-ci.org/raivieira/base-api-php)
[![codecov](https://codecov.io/gh/raivieira/base-api-php/branch/master/graph/badge.svg)](https://codecov.io/gh/raivieira/base-api-php)
[![GitHub issues](https://img.shields.io/github/issues/raivieira/base-api-php.svg)](https://github.com/raivieira/base-api-php/issues)
[![GitHub forks](https://img.shields.io/github/forks/raivieira/base-api-php.svg)](https://github.com/raivieira/base-api-php/network)
[![GitHub stars](https://img.shields.io/github/stars/raivieira/base-api-php.svg)](https://github.com/raivieira/base-api-php/stargazers)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/raivieira/base-api-php/master/LICENSE.md)


# Arquivos
```
src
├── Doctrine
│   ├── Common
│   │   └── Interfaces
│   │       ├── BaseEntityInterface.php
│   │       ├── BaseRepositoryInterface.php
│   │       ├── EntityManagerInterface.php
│   │       └── EntityTimestampInterface.php
│   ├── ODM
│   │   └── MongoDB
│   │       ├── Entities
│   │       │   └── BaseEntity.php
│   │       ├── EntityManager.php
│   │       └── Repositories
│   │           └── BaseRepository.php
│   └── ORM
│       ├── Entities
│       │   └── BaseEntity.php
│       └── Repositories
│           ├── BaseRepository.php
│           └── QueryWorker.php
├── Helpers
│   ├── CurlHelper.php
│   ├── FormatHelper.php
│   └── functions.php
└── Lumen
    ├── Application
    │   └── BaseApplication.php
    ├── Authentication
    │   └── JWT
    │       ├── Exceptions
    │       │   ├── NotPermissionAccessException.php
    │       │   └── RestrictAccessException.php
    │       ├── Interfaces
    │       │   ├── AuthRepositoryInterface.php
    │       │   └── JWTInterface.php
    │       ├── Libs
    │       │   ├── JWT.php
    │       │   └── User.php
    │       ├── Middleware
    │       │   └── AuthMiddleware.php
    │       └── Providers
    │           └── JWTServiceProvider.php
    ├── Http
    │   ├── Controllers
    │   │   ├── BaseController.php
    │   │   └── CRUDController.php
    │   └── Middleware
    │       └── CorsMiddleware.php
    ├── Providers
    │   ├── CustomConnectionSqlanywhereServiceProvider.php
    │   └── RegisterSymfonyConstraintsServiceProvider.php
    ├── Services
    │   ├── BaseService.php
    │   └── CRUDService.php
    └── Tests
        ├── BaseTest.php
        ├── Http
        │   └── Controllers
        │       ├── BaseControllerTest.php
        │       └── CRUDControllerTest.php
        ├── Repositories
        │   └── BaseRepositoryTest.php
        └── Services
            ├── BaseServiceTest.php
            └── CRUDServiceTest.php
```
