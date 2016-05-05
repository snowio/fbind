<?php
namespace Fbind;

abstract class Compiler
{
    private $binding;
    private $requiredBindingParams = [];
    private $optionalBindingParams = [];
    protected $bindingInvokationCode;
    private $paramBindCondition;

    public function __construct(callable $binding, callable $condition)
    {
        $this->binding = $binding;
        $this->paramBindCondition = $condition;

        $bindingParams = Internal\params_for($binding);

        foreach ($bindingParams as $paramName => $param) {
            if ($param->isOptional()) {
                $this->optionalBindingParams[$paramName] = $param;
            } else {
                $this->requiredBindingParams[$paramName] = $param;
            }
        }

        $this->bindingInvokationCode = sprintf(
            'call_user_func_array($this->binding, [%s])',
            $this->getArgumentsCode($bindingParams)
        );
    }

    /**
     * @param callable $subject
     * @return callable
     */
    public function compile(callable $subject)
    {
        $subjectParams = Internal\params_for($subject);
        $paramsToBind = array_filter($subjectParams, $this->paramBindCondition);

        if (0 == count($paramsToBind)) {
            return $subject;
        }

        $transport = (object)['subject' => $subject, 'binding' => $this->binding];
        $code = sprintf('unset($code); $compiledFn = %s;', $this->compileToCode($subjectParams, $paramsToBind));
        eval($code);
        $compiledFn = $compiledFn->bindTo($transport);

        return $compiledFn;
    }

    /**
     * @return string
     */
    abstract protected function compileToCode(array $subjectParams, array $paramsToBind);

    /**
     * @param \ReflectionParameter[] $subjectParams
     * @return string
     */
    protected function addBindingParams(array $subjectParams)
    {
        $requiredParams = $this->requiredBindingParams;
        $optionalParams = $this->optionalBindingParams;

        foreach ($subjectParams as $param) {
            $paramName = $param->getName();

            if ($param->isOptional()) {
                if (!isset($requiredParams[$paramName])) {
                    $optionalParams[$paramName] = $param;
                }
            } else {
                if (isset($optionalParams[$paramName])) {
                    unset($optionalParams[$paramName]);
                }
                $requiredParams[$paramName] = $param;
            }
        }

        $requiredParams = array_values($requiredParams);
        $optionalParams = array_values($optionalParams);

        return array_merge($requiredParams, $optionalParams);
    }

    /**
     * @param \ReflectionParameter[] $params
     * @return string
     */
    protected function getParamsCode(array $params)
    {
        $paramStrings = array_map([$this, 'getParamCode'], $params);

        return implode(', ', $paramStrings);
    }

    /**
     * @return string
     */
    private function getParamCode(\ReflectionParameter $param)
    {
        $result = preg_match(
            '{^Parameter #\d+ \[ <(required|optional)> (?<php_code>.+) ]\s*$}',
            (string)$param,
            $matches
        );

        if (!$result) {
            throw new \RuntimeException('Failed to get param PHP code using reflection.');
        }

        $matches['php_code'] = str_replace(' or NULL ', ' ', $matches['php_code']);

        return $matches['php_code'];
    }

    /**
     * @param \ReflectionParameter[] $params
     * @return string
     */
    protected function getBindingCode(array $paramsToBind)
    {
        $lines = array_map(function (\ReflectionParameter $param) {
            return sprintf('$%s = $this->bindingOutput;', $param->getName(), $this->bindingInvokationCode);
        }, $paramsToBind);

        return implode("\n", $lines);
    }

    /**
     * @param \ReflectionParameter[] $params
     * @return string
     */
    protected function getArgumentsCode(array $params)
    {
        $paramStrings = array_map([$this, 'getArgumentCode'], $params);

        return implode(', ', $paramStrings);
    }

    /**
     * @param \ReflectionParameter $param
     * @return string
     */
    private function getArgumentCode(\ReflectionParameter $param)
    {
        return '$' . $param->getName();
    }
}
