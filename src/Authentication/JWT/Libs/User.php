<?php

namespace Bludata\Authentication\JWT\Libs;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['usuarioId', 'empresaOrigemId','sistemaId'];
}