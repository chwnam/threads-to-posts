<?php

namespace Bojaghi\Continy\Tests;

use Bojaghi\Continy\ContinyFactory;
use WP_UnitTestCase;

class TestArgumentsCompletion extends WP_UnitTestCase
{
    public function test_argumentsCompletion(): void
    {
        $args = [
            'main'      => __DIR__ . '/DummyPlugin/dummy-plugin.php',
            'version'   => '1.0.0',
            'hooks'     => [],
            'bindings'  => [],
            'arguments' => [
                ArgumentsCompletion::class => ['success'],
            ],
        ];

        $continy  = ContinyFactory::create($args);
        $instance = $continy->get(ArgumentsCompletion::class);

        $this->assertEquals('success', $instance->value);
        $this->assertInstanceOf(CompletionDependency::class, $instance->dep);
        $this->assertEquals('passed', $instance->dep->value);;
    }
}


class ArgumentsCompletion
{
    public string               $value;
    public CompletionDependency $dep;

    public function __construct(string $value, CompletionDependency $dep)
    {
        $this->dep   = $dep;
        $this->value = $value;

        $this->dep->value = 'passed';
    }
}

class CompletionDependency
{
    public string $value;

    public function __construct()
    {
    }
}
