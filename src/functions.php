<?php
namespace Fbind;

require_once 'functions_internal.php';

/**
 * @param string $paramName
 * @return NonEmptyMatcher
 */
function bindParam($paramName)
{
    return new NonEmptyMatcher([Internal\param_is_named($paramName)]);
}

/**
 * @return Matcher
 */
function bindParams()
{
    return new Matcher;
}
