<?php
namespace Fbind;

class NonEmptyMatcher extends Matcher
{
    private $conditions = [];

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return NonEmptyMatcher
     */
    public function satisfying(callable $condition)
    {
        $conditions = $this->conditions;
        $conditions[] = $condition;

        return new NonEmptyMatcher($conditions);
    }

    /**
     * @param callable $binding
     * @return SynchronousCompiler
     */
    public function to(callable $binding)
    {
        $condition = function (\ReflectionParameter $parameter) {
            foreach ($this->conditions as $condition) {
                if (!$condition($parameter)) {
                    return false;
                }
            }

            return true;
        };

        return new SynchronousCompiler($binding, $condition);
    }

    /**
     * @param callable $binding A callable which returns a thenable
     * @return AsynchronousCompiler
     */
    public function toAsync(callable $binding)
    {
        $condition = function (\ReflectionParameter $parameter) {
            foreach ($this->conditions as $condition) {
                if (!$condition($parameter)) {
                    return false;
                }
            }

            return true;
        };

        return new AsynchronousCompiler($binding, $condition);
    }
}
