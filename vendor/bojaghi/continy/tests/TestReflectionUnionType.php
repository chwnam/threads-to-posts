<?php

namespace Bojaghi\Continy\Tests;

use Bojaghi\Continy\ContinyException;
use Bojaghi\Continy\ContinyNotFoundException;
use WP_UnitTestCase;
use function Bojaghi\Continy\Tests\DummyPlugin\getTestDummyPlugin;

class TestReflectionUnionType extends WP_UnitTestCase
{
    /**
     * @throws ContinyNotFoundException
     * @throws ContinyException
     */
    public function test_reflectionUnionType()
    {
        $continy = getTestDummyPlugin();

        $testClass = $continy->get(SimpleOptionalTestClass::class);
        $this->assertEquals('okay', $testClass->getString());

        $testClass = $continy->get(ReflectionUnionTypeTestClass::class, true);
        $this->assertEquals('okay', $testClass->getString());

        $testClass = $continy->get(ReflectionUnionTypeTestClassWithDefaultValue::class, true);
        $this->assertEquals('okay', $testClass->getString());

        $testClass = $continy->get(
            ReflectionUnionTypeTestClassWithDefaultValue::class,
            fn() => new ReflectionUnionTypeTestClassWithDefaultValue('improved')
        );
        $this->assertEquals('improved', $testClass->getString());
    }
}

class SimpleOptionalTestClass
{
    private string $string;

    public function __construct(string $string = 'okay')
    {
        $this->string = $string;
    }

    public function getString(): string
    {
        return $this->string;
    }
}

class ReflectionUnionTypeTestClass
{
    public function __construct(string|ReflectionUnionTypeTestParam_1|ReflectionUnionTypeTestParam_2 $param)
    {
    }

    public function getString(): string
    {
        return 'okay';
    }
}

class ReflectionUnionTypeTestClassWithDefaultValue
{
    private ReflectionUnionTypeTestParam_1|ReflectionUnionTypeTestParam_2|string $string;

    public function __construct(ReflectionUnionTypeTestParam_1|ReflectionUnionTypeTestParam_2|string $param = 'okay')
    {
        $this->string = $param;
    }

    public function getString(): string
    {
        return $this->string;
    }
}

class ReflectionUnionTypeTestParam_1
{
}

class ReflectionUnionTypeTestParam_2
{
}
