<?php

use PHPUnit\Framework\TestCase;
use Pre\Standard\Tests\HasExpand;
use Yay\Engine;

class ClassMethodExpanderTest extends TestCase
{
    use HasExpand;

    protected $macro = '
        $(macro) {
            $(\Pre\Standard\Parser\classMethod()) as classMethod
        } >> {
            $$(\Pre\Standard\Expander\classMethod($(classMethod)))
        }
    ';

    public function test_class_function_expansion()
    {
        $expected = <<<CODE
new class {
    public function greet(\$name = "world")
    {
        print "hello" . \$name;
    }

    private static function instance(): self
    {
        if (!static::\$instance) {
            static::\$instance = new static();
        }

        return static::\$instance;
    }

    function noop()
    {
    }
};
CODE;

        $actual = $this->expand($expected);

        $this->assertEquals($expected, $actual);
    }
}