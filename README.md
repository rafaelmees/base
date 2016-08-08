# Classes e funções básicas para criação de API's com PHP

# Diretórios
src
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
├── Entities
│   └── BaseEntity.php
├── Helpers
│   ├── CurlHelper.php
│   ├── FormatHelper.php
│   └── functions.php
├── Http
│   ├── Controllers
│   │   ├── BaseController.php
│   │   └── CRUDController.php
│   └── Middleware
│       └── CorsMiddleware.php
├── Providers
│   ├── CustomConnectionSqlanywhereServiceProvider.php
│   └── RegisterSymfonyConstraintsServiceProvider.php
├── Repositories
│   ├── BaseRepository.php
│   └── QueryWorker.php
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
