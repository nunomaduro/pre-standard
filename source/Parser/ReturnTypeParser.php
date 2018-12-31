<?php

namespace Pre\Standard\Parser;

use Pre\Standard\AbstractParser;
use function Pre\Standard\Internal\named;

use Yay\Parser;
use function Yay\buffer;
use function Yay\chain;

class ReturnTypeParser extends AbstractParser
{
    public function parse(string $prefix = null): Parser
    {
        return chain(buffer(":"), (new NullableTypeParser())->parse(named("return", $prefix)))
            ->as(named("returnType", $prefix))
            ->onCommit($this->onCommit);
    }
}
