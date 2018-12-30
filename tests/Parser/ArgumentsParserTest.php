<?php

use PHPUnit\Framework\TestCase;
use Pre\Standard\Tests\HasExpand;
use Yay\Engine;

class ArgumentsParserTest extends TestCase
{
    use HasExpand;

    protected $macro = '
        $(macro) {
            // https://github.com/marcioAlmada/yay/issues/56
            $(\Pre\Standard\Parser\arguments() as alias)
        } >> {
            $(alias ...(,) {
                $$(stringify(
                    $$(\Pre\Standard\Expander\argument($(argument)))
                ))
            })
        }
    ';

    public function test_basic_arguments()
    {
        $code = $this->expand('
            return [
                $one = "one", string $two = "two"
            ];
        ');

        $items = eval($code);

        $expected = ['$one = "one"', 'string $two = "two"'];

        $actual = array_map(function ($item) {
            return trim($item);
        }, $items);

        $this->assertEquals($expected, $actual);
    }
}
