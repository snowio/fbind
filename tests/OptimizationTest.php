<?php
class OptimizationTest extends PHPUnit_Framework_TestCase
{
    public function testMatchArrayParamByType()
    {
        $compiler = \Fbind\bindParam('baz')->ofType('array')->to(function () {
            return ['hello', 'world'];
        });

        $subject = function (array $foo, array $bar) {
            $this->assertSame(['hello', 'world'], $foo);
            $this->assertSame(['hello', 'world'], $bar);
        };

        $compiled = $compiler->compile($subject);

        $this->assertSame($subject, $compiled);
    }

    public function testBindingOnlyCalledOnce()
    {
        $compiler = \Fbind\bindParams()->ofType('array')->to(function () use (&$bindingWasCalled) {
            if ($bindingWasCalled) {
                $this->fail();
            }

            $bindingWasCalled = true;
            return ['hello', 'world'];
        });

        $subject = function (array $foo, array $bar) {
            $this->assertSame(['hello', 'world'], $foo);
            $this->assertSame(['hello', 'world'], $bar);
        };

        $compiled = $compiler->compile($subject);
        
        $compiled();
    }
}
