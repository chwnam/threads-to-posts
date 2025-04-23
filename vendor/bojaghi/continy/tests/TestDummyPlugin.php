<?php

namespace Bojaghi\Continy\Tests;

use Bojaghi\Continy\Continy;
use Bojaghi\Continy\Tests\DummyPlugin\IncompleteTester;
use Bojaghi\Continy\Tests\DummyPlugin\Supports\AliasedModuleSupport;
use Bojaghi\Continy\Tests\DummyPlugin\Supports\DummySupport;
use WP_UnitTestCase;
use function Bojaghi\Continy\Tests\DummyPlugin\getTestDummyPlugin;

/**
 * Class ContainerTest
 */
class TestDummyPlugin extends WP_UnitTestCase
{
    protected static string $pluginRoot;
    protected static string $pluginSetup;

    protected Continy $continy;

    public static function setUpBeforeClass(): void
    {
        self::$pluginRoot  = dirname(__FILE__) . '/DummyPlugin';
        self::$pluginSetup = self::$pluginRoot . '/conf/setup.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->continy = getTestDummyPlugin();
    }

    public function test_getMain()
    {
        $this->assertEquals($this->continy->getMain(), self::$pluginRoot . '/dummy-plugin.php');
    }

    public function test_getVersion()
    {
        $array   = include self::$pluginSetup;
        $version = $array['version'] ?? false;

        $this->assertEquals($this->continy->getVersion(), $version);
    }

    public function test_get_ReuseTester()
    {
        $className = \Bojaghi\Continy\Tests\DummyPlugin\ReuseTester::class;

        // Make sure the instance is created.
        $count = $className::$constructCount;
        $this->assertInstanceOf($className, $this->continy->get($className));
        $this->assertEquals($count + 1, $className::$constructCount);

        // The instance should be re-used. Thus, the count is unchanged.
        $this->continy->get($className);
        $this->assertEquals($count + 1, $className::$constructCount);

        // Manual construction should increase the count.
        new $className();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        $this->assertEquals($count + 2, $className::$constructCount);
    }

    public function test_get_ModuleCPT()
    {
        $className = \Bojaghi\Continy\Tests\DummyPlugin\Modules\CPT::class;

        // Test if CPT can get by alias
        $this->assertInstanceOf($className, $this->continy->modCPT);

        // Test if module can get by FQCN.
        $instance = $this->continy->get($className);
        $this->assertInstanceOf($className, $instance);

        // Test if the post type is registered.
        $this->assertTrue(post_type_exists('dummy_type'));
        $this->assertFalse(post_type_exists('unknown_type'));

        // Test injected variable.
        $this->assertEquals(20, $instance->foo);
    }

    public function test_get_Binding_IDummy()
    {
        // Test interface - implementation binding.
        // By setup, DummyTypeOne is bound to interface.
        $Interface   = \Bojaghi\Continy\Tests\DummyPlugin\Dummies\IDummy::class;
        $TypeOne     = \Bojaghi\Continy\Tests\DummyPlugin\Dummies\DummyTypeOne::class;
        $instanceOne = $this->continy->get($Interface);
        $this->assertInstanceOf($Interface, $instanceOne);
        $this->assertInstanceOf($TypeOne, $instanceOne);
        // Test argument injection.
        $this->assertEquals('dummy-interface', $instanceOne->dummyMethod());

        // Explicitly call DummyTypeTwo.
        $TypeTwo     = \Bojaghi\Continy\Tests\DummyPlugin\Dummies\DummyTypeTwo::class;
        $instanceTwo = $this->continy->get($TypeTwo);
        $this->assertInstanceOf($Interface, $instanceTwo);
        $this->assertInstanceOf($TypeTwo, $instanceTwo);
        // Test argument injection.
        $this->assertEquals('dummy-type-two', $instanceTwo->dummyMethod());
    }

    public function test_get_ReflectionInjection()
    {
        $tester = $this->continy->reflectionInjectionTester;
        $this->assertInstanceOf(
            \Bojaghi\Continy\Tests\DummyPlugin\ReflectionInjection\ReflectionTester::class,
            $tester,
        );

        // $tester's dependency one in __construct()
        $this->assertInstanceOf(
            \Bojaghi\Continy\Tests\DummyPlugin\ReflectionInjection\DependencyOne::class,
            $tester->dependencyOne,
        );

        // $tester's dependency one->oneOne in __construct(), chained
        $this->assertInstanceOf(
            \Bojaghi\Continy\Tests\DummyPlugin\ReflectionInjection\DependencyOneOne::class,
            $tester->dependencyOne->oneOne,
        );

        // $tester's dependency two in __construct()
        $this->assertInstanceOf(
            \Bojaghi\Continy\Tests\DummyPlugin\ReflectionInjection\DependencyTwo::class,
            $tester->dependencyTwo,
        );

        // $tester's dependency tow->twoOne in __construct(), chained
        // twoOne interface check
        $this->assertInstanceOf(
            \Bojaghi\Continy\Tests\DummyPlugin\ReflectionInjection\IDependencyTwoOne::class,
            $tester->dependencyTwo->twoOne,
        );
        // twoOne class check
        $this->assertInstanceOf(
            \Bojaghi\Continy\Tests\DummyPlugin\ReflectionInjection\DependencyTwoOne::class,
            $tester->dependencyTwo->twoOne,
        );
    }

    public function test_dummySupport()
    {
        $ds = $this->continy->get('ds');
        $this->assertInstanceOf(DummySupport::class, $ds);
        $this->assertEquals(20, $ds->foo);
    }

    public function test_constructorCall()
    {
        $instance = $this->continy->get(
            \Bojaghi\Continy\Tests\DummyPlugin\ConstructorCall\ConstructorCall::class,
            function ($continy) {
                return new \Bojaghi\Continy\Tests\DummyPlugin\ConstructorCall\ConstructorCall(
                    'Continy unit test',
                    'Test is success',
                );
            },
        );

        $this->assertEquals('Continy unit test', $instance->getVar1());
        $this->assertEquals('Test is success', $instance->getVar2());

        // You cannot get a proper instance because the class is not configured.
        // Calling get() with constructor call does not re-use instances.
        $ref  = new \ReflectionClass(Continy::class);
        $prop = $ref->getProperty('storage');
        $prop->setAccessible(true);
        // Therefore, ConstructorCall class cannot be found in the storage.
        $storage = $prop->getValue($this->continy);
        $this->assertArrayNotHasKey(
            \Bojaghi\Continy\Tests\DummyPlugin\ConstructorCall\ConstructorCall::class,
            $storage,
        );
    }

    public function test_incompleteArguments(): void
    {
        $instance = $this->continy->get(
            \Bojaghi\Continy\Tests\DummyPlugin\IncompleteTester::class,
        );

        $this->assertInstanceOf(IncompleteTester::class, $instance);
    }

    /**
     * Test if continy can instantiate aliased module by FQCN.
     *
     * - In 'bindings' section of settings, you can find 'aliasedModule' section.
     * - In 'arguments' section of settings, you can find 'aliasModule' section.
     * - In this method continy tries to get the object by FQCN. It should be successful
     *
     * @return void
     */
    public function test_aliasedModule(): void
    {
        $value = $this->continy->get(AliasedModuleSupport::class)->getValue();
        $this->assertEquals('success', $value);
    }
}
