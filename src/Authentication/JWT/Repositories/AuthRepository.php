<?php

namespace Bludata\Authentication\JWT\Repositories;

use Bludata\Authentication\JWT\Interfaces\AuthRepositoryInterface;
use Bludata\Authentication\JWT\Interfaces\JWTInterface;
use Bludata\Authentication\JWT\Exceptions\NotPermissionAccessException;

class AuthRepository implements AuthRepositoryInterface
{
    protected $jwt;

    public function __construct(JWTInterface $jwt)
    {
        $this->jwt = $jwt;
    }

    public function getUserLoggedByToken($token)
    {
        try
        {
            if (!$token = $this->jwt->decodeToken($token))
            {
                throw new NotPermissionAccessException();
            }
            else if (!$this->jwt->isValidByToken($token))
            {
                throw new NotPermissionAccessException();
            }
            else if (!$userLogged = $token->getClaim('user'))
            {
                throw new NotPermissionAccessException();
            }
            else if (!isset($userLogged->usuarioId) || !isset($userLogged->empresaOrigemId))
            {
                throw new NotPermissionAccessException();
            }

            return $userLogged;
        }
        catch (NotPermissionAccessException $e)
        {
            abort(401, $e->getMessage());
        }
    }
}
