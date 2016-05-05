<?php
class PromiseTest extends PHPUnit_Framework_TestCase
{
    public function testMatchArrayParamByType()
    {
        $compiler = \Fbind\bindParams()->ofType('array')->toAsync(function () {
            return \React\Promise\resolve(['hello', 'world']);
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
        $compiler = \Fbind\bindParam('foo')->toAsync(function () {
            return \React\Promise\resolve(['hello', 'world']);
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
        $compiler = \Fbind\bindParam('foo')->ofType('array')->toAsync(function () {
            return \React\Promise\resolve(['hello', 'world']);
        });

        $subject = function (array $foo, array $bar) {
            $this->assertSame(['hello', 'world'], $foo);
            $this->assertSame(['foobar'], $bar);
        };

        $compiled = $compiler->compile($subject);

        $compiled(['foobar']);
    }

    public function testSubjectIsAllowedParamName()
    {
        $compiler = \Fbind\bindParam('subject')->toAsync(function () {
            return \React\Promise\resolve(['hello', 'world']);
        });

        $compiled = $compiler->compile(function (array $subject, array $bar) {
            $this->assertSame(['hello', 'world'], $subject);
            $this->assertSame(['foobar'], $bar);
        });

        $compiled(['foobar']);
    }
}
