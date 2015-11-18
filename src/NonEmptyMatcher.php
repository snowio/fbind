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
     * @return Compiler
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

        return new Compiler($binding, $condition);
    }
}
