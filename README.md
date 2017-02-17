# Base API

[![Build Status](https://travis-ci.org/raivieira/base-api-php.svg?branch=master)](https://travis-ci.org/raivieira/base-api-php)
[![StyleCI](https://styleci.io/repos/56002039/shield)](https://styleci.io/repos/56002039)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/raivieira/base-api-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/raivieira/base-api-php/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/57b6e834090d4d00328f4eb3/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57b6e834090d4d00328f4eb3)
[![codecov](https://codecov.io/gh/raivieira/base-api-php/branch/master/graph/badge.svg)](https://codecov.io/gh/raivieira/base-api-php)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/raivieira/base-api-php/master/LICENSE.md)

## Setup

### Docker
```sh
$ curl -sSL https://get.docker.com/ | sh
```

### Gerando chave SSH
```sh
$ ssh-keygen -t rsa -b 4096 -C "nome.sobrenome@bludata.com.br"
```

## Como instalar

Alterar ``composer.json`` adicionado o reposit�rio do bitbucket, segue exemplo:

```json
{
    "require": {
        // ...
        "bludata/base": "v2.2.4"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git@bitbucket.org:bludata/base.git"
        }
    ]
}
```