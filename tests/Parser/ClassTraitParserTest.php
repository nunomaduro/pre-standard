<?php

use PHPUnit\Framework\TestCase;
use Pre\Standard\Tests\HasExpand;
use Yay\Engine;

class ClassTraitParserTest extends TestCase
{
    use HasExpand;

    protected $macro = '
        $(macro) {
            // https://github.com/marcioAlmada/yay/issues/56
            $(\Pre\Standard\Parser\classTrait() as alias)
        } >> {
            $(alias ... {
                $(classTraitNames ... {
                    $$(stringify($(classTraitName))),
                })

                $(classTraitBody ? {
                    $(classTraitBody ... {
                        $(classTraitAliases ... {
                            $(classTraitAlias ... {
                                $$(stringify($(classTraitAliasLeft))),

                                $(classTraitAliasInsteadOf ? {
                                    "insteadof",
                                })

                                $(classTraitAliasAs ? {
                                    "as",

                                    $(classTraitAliasAs ... {
                                        $(classTraitAliasVisibilityModifiers ? {
                                            $(classTraitAliasVisibilityModifiers ...(,) {
                                                $(classTraitAliasVisibilityModifier ... {
                                                    $$(stringify($(classTraitAliasVisibilityModifier)))
                                                })
                                            }),
                                        })
                                    })
                                })

                                $$(stringify($(classTraitAliasRight))),
                            })
                        })
                    })
                })

                $(classTraitBody ! {
                    "no body",
                })
            })
        }
    ';

    public function test_identifies_class_traits()
    {
        $code = $this->expand('
            return [
                [ use \Foo ],
                [ use Foo, Bar, Foo\Bar\Baz ],
                [ use Foo { bar as baz } ],
                [ use Foo { bar as baz; } ],
                [ use Foo { bar as protected baz; } ],
                [ use Foo, Bar { Bar::baz insteadof Foo::baz; } ],
                [ use Foo, Bar { Bar::baz insteadof Foo::baz; Foo::bar as public boo; } ],
            ];
        ');

        $this->assertEquals(
            [
                ['\\Foo', 'no body'],
                ['Foo', 'Bar', 'Foo\\Bar\\Baz', 'no body'],
                ['Foo', 'bar', 'as', 'baz'],
                ['Foo', 'bar', 'as', 'baz'],
                ['Foo', 'bar', 'as', 'protected', 'baz'],
                ['Foo', 'Bar', 'Bar::baz', 'insteadof', 'Foo::baz'],
                ['Foo', 'Bar', 'Bar::baz', 'insteadof', 'Foo::baz', 'Foo::bar', 'as', 'public', 'boo'],
            ],
            eval($code)
        );
    }
}
