<?php

if (!defined('ABSPATH')) {
    exit;
}

use Bojaghi\Continy\Continy;
use Bojaghi\Continy\Tests\DummyPlugin;

return [
    'main_file' => dirname(__DIR__) . '/dummy-plugin.php',
    'version'   => '1.0.0',

    // Hooks definition
    'hooks'     => [
        'admin_init'  => 0,
        'init'        => 0,
        'test_action' => 0,
    ],

    // Objects binding
    'bindings'  => [
        'modCPT'                                                 => DummyPlugin\Modules\CPT::class,
        'reflectionInjectionTester'                              => DummyPlugin\ReflectionInjection\ReflectionTester::class,
        // IDummy implementation
        DummyPlugin\Dummies\IDummy::class                        => DummyPlugin\Dummies\DummyTypeOne::class,
        DummyPlugin\ReflectionInjection\IDependencyTwoOne::class => DummyPlugin\ReflectionInjection\DependencyTwoOne::class,
        // Support binding
        'ds'                                                     => DummyPlugin\Supports\DummySupport::class,

        // aliasedModule: continy->get() method should successfully grab the original class name by alias.
        'aliasedModule'                                          => DummyPlugin\Modules\AliasedModule::class,
    ],

    // Modules setting
    'modules'   => [
        '_'    => [
            function () {
            },
        ],
        'init' => [
            Continy::PR_DEFAULT => [
                'modCPT',
                function () {
                },
            ],
        ],
    ],

    // Argument injection
    'arguments' => [
        'modCPT'                                => [
            'foo' => 20,
        ],
        DummyPlugin\Dummies\IDummy::class       => [
            'dummy' => 'interface',
        ],
        DummyPlugin\Dummies\DummyTypeTwo::class => function () {
            return [
                'dummy' => 'type-two',
            ];
        },
        'ds'                                    => [
            'foo' => 20,
        ],
        // FunctionalCall
        Continy::concatName(
            DummyPlugin\FunctionCall\FunctionCall::class,
            'configuredCall',
        )                                       => [
            'x' => 'Keyboard',
            'y' => 'Mouse',
        ],
        // Incomplete arguments
        DummyPlugin\IncompleteTester::class     => [3],

        // Aliased
        'aliasedModule' => ['success'],
    ],
];
