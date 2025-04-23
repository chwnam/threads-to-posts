<?php

namespace Bojaghi\FieldsRender\Tests;

use Bojaghi\FieldsRender\AdminFormTable;
use Bojaghi\FieldsRender\Render as R;

class TestAdminFormTable extends \WP_UnitTestCase
{
    public function tearDown(): void
    {
        R::flushStack();
    }

    public function testTableOpen()
    {
        $result = AdminFormTable::tableOpen();

        $this->assertIsString($result);
        $this->assertStringStartsWith('<table', $result);
        $this->assertStringContainsString('class="form-table"', $result);
        $this->assertStringContainsString('role="presentation"', $result);
    }

    public function testTrOpen()
    {
        $result = AdminFormTable::trOpen();

        $this->assertIsString($result);
        $this->assertStringStartsWith('<tr', $result);
    }

    public function testThOpen()
    {
        $result = AdminFormTable::thOpen();

        $this->assertIsString($result);
        $this->assertStringStartsWith('<th', $result);
        $this->assertStringContainsString('scope="row"', $result);
    }

    public function testTdOpen()
    {
        $result = AdminFormTable::tdOpen();
        $this->assertIsString($result);
        $this->assertStringContainsString('<td', $result);
    }
}