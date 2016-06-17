# base-api-php 

##Autenticação via JWT

No arquivo `.env` registre a variável:
```
JWT_SECRET = chave-secreta-aqui
```

No arquivo `bootstrap\app.php` adicione as seguintes linhas:
```
$app->routeMiddleware([
    'auth' => Bludata\Authentication\JWT\Middleware\AuthMiddleware::class
]);

$app->register(Bludata\Authentication\JWT\Providers\JWTServiceProvider::class);
```

Não esqueça de adicionar a middleware `auth` nas suas rotas:
```
$app->get('minha-rota', ['middleware' => 'auth', function () {
    //
}]);
```

Agora nos `headers` da sua requisição basta adicionar `Authorization: seu-token`
