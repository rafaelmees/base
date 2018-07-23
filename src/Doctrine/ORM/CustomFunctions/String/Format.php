<?php

namespace Bludata\Doctrine\ORM\CustomFunctions\String;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class Format extends FunctionNode
{
    /*
     * holds the timestamp of the DATE_FORMAT DQL statement
     * @var mixed
     */
    protected $dateExpression;

    /**
     * holds the '%format' parameter of the DATE_FORMAT DQL statement.
     *
     * @var string
     */
    protected $formatChar;

    /**
     * getSql - allows ORM  to inject a DATE_FORMAT() statement into an SQL string being constructed.
     *
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     *
     * @return void
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'FORMAT('.
                $sqlWalker->walkArithmeticExpression($this->dateExpression).
                ','.
                $sqlWalker->walkStringPrimary($this->formatChar).
                ')';
    }

    /**
     * parse - allows DQL to breakdown the DQL string into a processable structure.
     *
     * @param \Doctrine\ORM\Query\Parser $parser
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->dateExpression = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_COMMA);

        $this->formatChar = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
