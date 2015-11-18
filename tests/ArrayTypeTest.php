<?php
class ArrayTypeTest extends PHPUnit_Framework_TestCase
{
    public function testMatchArrayParamByType()
    {
        $compiler = \Fbind\bindParams()->ofType('array')->to(function () {
            return ['hello', 'world'];
        });

        $subject = function (array $foo, array $bar) {
            $this->assertSame(['hello', 'world'], $foo);
            $this->assertSame(['hello', 'world'], $bar);
        };

        $compiled = $compiler->compile($subject);

        $compiled();
    }

    public function testMatchArrayParamByName()
    {
        $compiler = \Fbind\bindParam('foo')->to(function () {
            return ['hello', 'world'];
        });

        $subject = function (array $foo, array $bar) {
            $this->assertSame(['hello', 'world'], $foo);
            $this->assertSame(['foobar'], $bar);
        };

        $compiled = $compiler->compile($subject);

        $compiled(['foobar']);
    }

    public function testMatchArrayParamByNameAndType()
    {
        $compiler = \Fbind\bindParam('foo')->ofType('array')->to(function () {
            return ['hello', 'world'];
        });

        $subject = function (array $foo, array $bar) {
            $this->assertSame(['hello', 'world'], $foo);
            $this->assertSame(['foobar'], $bar);
        };

        $compiled = $compiler->compile($subject);

        $compiled(['foobar']);
    }
}
