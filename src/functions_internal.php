<?php
namespace Fbind\Internal;

/**
 * @return \ReflectionParameter[]
 */
function params_for(callable $callback)
{
    if (is_array($callback)) {
        $callbackReflection = new \ReflectionMethod($callback[0], $callback[1]);
    } elseif (is_object($callback) && !$callback instanceof \Closure) {
        $callbackReflection = new \ReflectionObject($callback);
        $callbackReflection = $callbackReflection->getMethod('__invoke');
    } else {
        $callbackReflection = new \ReflectionFunction($callback);
    }

    return $callbackReflection->getParameters();
}

/**
 * @param string $name
 * @return \Closure
 */
function param_is_named($name)
{
    return function (\ReflectionParameter $param) use ($name) {
        return $name === $param->getName();
    };
}

/**
 * @param callable $condition
 * @return \Closure
 */
function param_name_satisfies(callable $condition)
{
    return function (\ReflectionParameter $param) use ($condition) {
        return $condition($param->getName());
    };
}

/**
 * @param string $class
 * @return \Closure
 */
function param_is_superclass_of($class)
{
    return param_class_satisfies(function ($paramClass) use ($class) {
        return is_subclass_of($class, $paramClass);
    });
}

/**
 * @return \Closure
 */
function param_type_satisfies(callable $condition)
{
    return function (\ReflectionParameter $param) use ($condition) {
        $type = param_type($param);
        return !is_null($type) && $condition($type);
    };
}

/**
 * @return callable
 */
function param_class_satisfies(callable $condition)
{
    return function (\ReflectionParameter $param) use ($condition) {
        $class = $param->getClass();
        return !is_null($class) && $condition($class->getName());
    };
}

/**
 * @return \Closure
 */
function one_of()
{
    $conditions = func_get_args();

    return function (\ReflectionParameter $param) use ($conditions) {
        foreach ($conditions as $condition) {
            if (call_user_func($condition, $param)) {
                return true;
            }
        }

        return false;
    };
}

/**
 * @return null|string
 */
function param_type(\ReflectionParameter $param)
{
    if (null !== $class = $param->getClass()) {
        return $class->name;
    }

    if ($param->isArray()) {
        return 'array';
    }

    if ($param->isCallable()) {
        return 'callable';
    }

    return null;
}
