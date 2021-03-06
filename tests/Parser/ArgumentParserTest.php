<?php

namespace Pre\Standard\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Pre\Standard\Tests\HasExpand;
use Yay\Engine;

class ArgumentParserTest extends TestCase
{
    use HasExpand;

    protected $macro = '
        $(macro) {
            $(\Pre\Standard\Parser\argument())
        } >> {
            $(argument ... {
                $(nullableType ? {
                    $(nullableType ... {
                        $(nullable ? {
                            "nullable",
                        })

                        $(type ? {
                            $$(stringify($(type))),
                        })
                    })
                })

                $$(stringify($(name))),

                $(assignment ? {
                    $(assignment ... {
                        "equals",
    
                        $(new ? {
                            "new",
                        })
    
                        $$(stringify($(value)))
                    })
                })
            })
        }
    ';

    public function test_identifies_arguments_with_assignment()
    {
        $code = $this->expand('
            return [
                $thing = "param"
            ];
        ');

        $this->assertEquals(['$thing', 'equals', '"param"'], eval($code));
    }

    public function test_identifies_arguments_with_object_type_and_object_assignment()
    {
        $code = $this->expand('
            return [
                ? \Foo\Bar\Baz $thing = new \Foo\Bar\Baz("param")
            ];
        ');

        $this->assertEquals(
            ['nullable', '\Foo\Bar\Baz', '$thing', 'equals', 'new', '\Foo\Bar\Baz("param")'],
            eval($code)
        );
    }

    public function test_identifies_arguments_with_object_type_and_function_assignment()
    {
        $code = $this->expand('
            return [
                ? \Foo\Bar\Baz $thing = \Foo\Bar\baz("param")
            ];
        ');

        $this->assertEquals(['nullable', '\Foo\Bar\Baz', '$thing', 'equals', '\Foo\Bar\baz("param")'], eval($code));
    }

    public function test_identifies_arguments_with_function_assignment()
    {
        $code = $this->expand('
            return [
                $thing = \Foo\Bar\baz("param")
            ];
        ');

        $this->assertEquals(['$thing', 'equals', '\Foo\Bar\baz("param")'], eval($code));
    }
}
