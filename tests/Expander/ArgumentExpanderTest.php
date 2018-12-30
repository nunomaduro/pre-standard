<?php

use PHPUnit\Framework\TestCase;
use Pre\Standard\Tests\HasExpand;
use Yay\Engine;

class ArgumentExpanderTest extends TestCase
{
    use HasExpand;

    protected $macro = "
        $(macro) {
            $(\Pre\Standard\Parser\argument() as alias)
        } >> {
            $$(\Pre\Standard\Expander\argument($(alias)))
        }
    ";

    public function test_basic_argument()
    {
        $expected = '$thing';
        $actual = $this->expand($expected);

        $this->assertEquals($expected, $actual);
    }

    public function test_basic_argument_with_assignment()
    {
        $expected = '$thing = "param"';
        $actual = $this->expand($expected);

        $this->assertEquals($expected, $actual);
    }

    public function test_full_argument()
    {
        $expected = '? \Foo\Bar\Obj $thing = new \Foo\Bar\Obj("param")';
        $actual = $this->expand($expected);

        $this->assertEquals($expected, $actual);
    }
}