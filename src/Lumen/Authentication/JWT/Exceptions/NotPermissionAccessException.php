<?php

namespace Bludata\Lumen\Authentication\JWT\Exceptions;

final class NotPermissionAccessException extends \Exception
{
    protected $message = 'Área restrita! Você não tem permissão de acesso.';
}
