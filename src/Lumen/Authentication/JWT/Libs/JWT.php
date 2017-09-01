<?php

namespace Bludata\Lumen\Authentication\JWT\Libs;

use Bludata\Lumen\Authentication\JWT\Interfaces\JWTInterface;
use Exception;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;

/**
 * Basic gadget for using of the JWT.
 *
 * @author Cristian B. dos Santos <cristian.deveng@gmail.com>
 */
class JWT implements JWTInterface
{
    /**
     * The builder token.
     *
     * @var Lcobucci\JWT\Builder
     */
    protected $builder;

    public function __construct()
    {
        $this->builder = new Builder();

        $this->builder
             ->setIssuer(gethostname())
             ->setId(time(), true);
        // ->setIssuedAt(time())
             // ->setNotBefore(time() + 60)
             // ->setExpiration(time() + 3600)
             // ->set('teste', 1);
    }

    /**
     * Create object of a token.
     *
     * @param array $user
     *
     * @return Lcobucci\JWT\Token
     */
    public function generateTokenByUser($user)
    {
        return $this->builder
                    ->set('user', $user)
                    ->sign(new Sha256(), env('JWT_SECRET'))
                    ->getToken();
    }

    /**
     * [Return the value of a token the request].
     *
     * @param string $token
     *
     * @return Lcobucci\JWT\Token
     */
    public function decodeToken($token)
    {
        try {
            $parser = new Parser();

            if (!$token = $parser->parse((string) $token)) {
                throw new Exception('Token inválido');
            }

            return $token;
        } catch (Exception $e) {
            abort(401, 'Favor efetuar o login novamente');
        }
    }

    /**
     * Verify is validate token in signature.
     *
     * @param Lcobucci\JWT\Token $token
     *
     * @return bool
     */
    public function isValidByToken(Token $token)
    {
        return $token->verify(new Sha256(), env('JWT_SECRET'));
    }
}
