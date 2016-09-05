<?php

namespace Bludata\Common\Traits;

trait GetParamsTrait
{
    public function getParams()
    {
        $params = array_keys(
            get_class_vars(
                get_class($this)
            )
        );

        return array_combine(
            $params,
            array_map(
                function ($input) {
                    $input = sprintf('get%s', ucfirst($input));
                    return $this->$input();
                },
                $params
            )
        );
    }
}
