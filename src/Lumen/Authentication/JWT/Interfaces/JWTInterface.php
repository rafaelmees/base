<?php

namespace Bludata\Lumen\Authentication\JWT\Interfaces;

use Lcobucci\JWT\Token;

/**
 * Basic gadget for using of the JWT.
 *
 * @author Cristian B. dos Santos <cristian.deveng@gmail.com>
 */
interface JWTInterface
{
    /**
     * Create object of a token.
     *
     * @param array $user
     *
     * @return Lcobucci\JWT\Token
     */
    public function generateTokenByUser($user);

    /**
     * [Return the value of a token the request].
     *
     * @param string $token
     *
     * @return Token|null
     */
    public function decodeToken($token);

    /**
     * Verify is validate token in signature.
     *
     * @param Token $token
     *
     * @return bool
     */
    public function isValidByToken(Token $token);
}
