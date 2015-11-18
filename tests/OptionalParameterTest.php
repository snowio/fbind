<?php
class OptionalParameterTest extends PHPUnit_Framework_TestCase
{
    public function testOptionalParameterNotProvided()
    {
        $compiler = \Fbind\bindParams()->ofType('stdClass')->to(function () {
            return null;
        });

        $subject = function (stdClass $foo = null) {
            $this->assertNull($foo);
        };

        $compiled = $compiler->compile($subject);

        $compiled();
    }

    public function testMultipleCompilers()
    {
        $now = time();

        $timestampCompiler = \Fbind\bindParam('timestamp')->to(function () use ($now) {
            return $now;
        });

        $dateTimeCompiler = \Fbind\bindParams()->ofType(\DateTime::class)->to(function ($timestamp) {
            $dateTime = new DateTime;
            $dateTime->setTimestamp($timestamp);
            return $dateTime;
        });

        $subject = function ($timestamp, \DateTime $dateTime) use ($now) {
            $this->assertEquals($now, $timestamp);
            $this->assertEquals($now, $dateTime->getTimestamp());
        };

        $compiled = $timestampCompiler->compile($dateTimeCompiler->compile($subject));

        $compiled();
    }
}
