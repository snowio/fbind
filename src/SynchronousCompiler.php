<?php
namespace Fbind;

class SynchronousCompiler extends Compiler
{
    protected function compileToCode(array $subjectParams, array $paramsToBind)
    {
        /** @var string[] $paramsToProxy The params which will be passed straight through to the subject */
        $paramsToProxy = array_diff_key($subjectParams, $paramsToBind);
        /** @var string[] $paramsToProxy All params required by the new, compiled function */
        $params = $this->addBindingParams($paramsToProxy);

        $code = "
        function ({$this->getParamsCode($params)}) {
            \$this->bindingOutput = {$this->bindingInvokationCode};
            {$this->getBindingCode($paramsToBind)}
            return call_user_func_array(\$this->subject, [{$this->getArgumentsCode($subjectParams)}]);
        }";

        return $code;
    }
}
