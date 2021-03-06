<?php

namespace Pre\Standard\Parser;

use Pre\Standard\AbstractParser;

use Yay\Parser;
use function Yay\buffer;
use function Yay\chain;
use function Yay\expression;
use function Yay\optional;
use function Yay\token;

class ArgumentParser extends AbstractParser
{
    public function parse(): Parser
    {
        return chain(
            optional((new NullableTypeParser())->parse()),
            token(T_VARIABLE)->as("name"),
            optional(chain(buffer("="), optional(buffer("new"))->as("new"), expression()->as("value")))->as(
                "assignment"
            )
        )->as("argument");
    }
}
