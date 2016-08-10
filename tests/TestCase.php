<?php

use SebastianBergmann\PeekAndPoke\Proxy;

class Testcase extends PHPUnit_Framework_TestCase {

    protected $faker;

    public function proxy($object)
    {
        return new Proxy($object);
    }

    public function faker()
    {
        if (is_null($this->faker)) {
            $this->faker = Faker\Factory::create('pt_Br');
        }

        return $this->faker;
    }

    public function app()
    {
        global $app;

        if (func_num_args()) {
            return $app->make(func_get_arg(0));
        }

        return $app;
    }

    public function setObjectAttribute($object, $attribute, $value)
    {
        $proxy = $this->proxy($object);
        $proxy->$attribute = $value;
        return $object;
    }
}
