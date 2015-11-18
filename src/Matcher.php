<?php
namespace Fbind;

class Matcher
{
    /**
     * @param $type
     * @return NonEmptyMatcher
     */
    public function ofExactType($type)
    {
        $condition = Internal\param_type_satisfies(function ($paramType) use ($type) {
            return $type === $paramType;
        });

        return $this->satisfying($condition);
    }

    /**
     * @param string $type
     * @return NonEmptyMatcher
     */
    public function ofType($type)
    {
        return $this->satisfying(Internal\one_of(
            Internal\param_type_satisfies(function ($paramType) use ($type) {
                return $type === $paramType;
            }),
            Internal\param_is_superclass_of($type)
        ));
    }

    /**
     * @return NonEmptyMatcher
     */
    public function satisfying(callable $condition)
    {
        return new NonEmptyMatcher([$condition]);
    }
}
