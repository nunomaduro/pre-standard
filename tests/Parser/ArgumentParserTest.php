<?php

use PHPUnit\Framework\TestCase;
use Pre\Standard\Tests\HasExpand;
use Yay\Engine;

class ArgumentParserTest extends TestCase
{
    use HasExpand;

    protected $macro = '
        $(macro) {
            // https://github.com/marcioAlmada/yay/issues/56
            $(\Pre\Standard\Parser\argument() as alias)
        } >> {
            $(alias ... {
                $(argumentNullableType ? {
                    $(argumentNullableType ... {
                        $(argumentNullable ? {
                            "nullable",
                        })

                        $(argumentType ? {
                            $$(stringify($(argumentType))),
                        })
                    })
                })

                $$(stringify($(argumentName))),

                $(argumentAssignment ? {
                    $(argumentAssignment ... {
                        "equals",
    
                        $(argumentNew ? {
                            "new",
                        })
    
                        $$(stringify($(argumentValue)))
                    })
                })
            })
        }
    ';

    public function test_basic_argument()
    {
        $code = $this->expand('
            return [
                $thing = "param"
            ];
        ');

        $this->assertEquals(['$thing', 'equals', '"param"'], eval($code));
    }

    public function test_full_argument_with_object()
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

    public function test_full_argument_with_function()
    {
        $code = $this->expand('
            return [
                ? \Foo\Bar\Baz $thing = \Foo\Bar\baz("param")
            ];
        ');

        $this->assertEquals(['nullable', '\Foo\Bar\Baz', '$thing', 'equals', '\Foo\Bar\baz("param")'], eval($code));
    }
}
