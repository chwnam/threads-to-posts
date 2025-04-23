<?php

namespace Bojaghi\FieldsRender\Tests;

use Bojaghi\FieldsRender\Render as R;

class TestRender extends \WP_UnitTestCase
{
    public function tearDown(): void
    {
        R::flushStack();
    }

    public function test_open_close()
    {
        $this->assertEquals(
            '<div></div>',
            R::open('div') . R::close(),
        );

        $this->assertEquals(
            '<input/>',
            R::open('input', '', true),
        );

        R::open('nav');
        $this->assertEquals(
            ['nav'],
            R::getStack(),
        );
    }

    public function test_attrs()
    {
        $this->assertEquals(' class="test"', R::attrs(['class' => 'te잘못된형식st ' . urlencode('잘못된형식')]));
        $this->assertEquals(' autoplay="autoplay"', R::attrs(['autoplay' => true]));
        $this->assertEquals(' selected="selected"', R::attrs(['selected' => true]));
        $this->assertEquals(' selected="selected"', R::attrs(['selected' => 'selected']));
        $this->assertEquals('', R::attrs(['selected' => '']));
        $this->assertEquals('', R::attrs(['selected' => false]));
    }

    public function test_checkbox(): void
    {
        // Hidden should be cancelled.
        $this->assertEquals(
            '<input id="cb-id" name="cb_name" type="checkbox" checked="checked"/><label for="cb-id">label</label>',
            R::checkbox('label', true, "id=cb-id&name=cb_name&type=hidden"),
        );

        // Checked should be omitted.
        $this->assertEquals(
            '<input id="cb-id" name="cb_name" type="checkbox"/><label for="cb-id">label</label>',
            R::checkbox('label', false, "id=cb-id&name=cb_name&type=hidden"),
        );
    }

    public function test_input(): void
    {
        $this->assertEquals(
            '<input id="id" name="name" type="text" value="value" onclick="alert(msg);"/>',
            R::input("id=id&name=name&type=text&value=value&onclick=alert(msg);"),
        );
    }

    public function test_label(): void
    {
        $this->assertEquals(
            '<label for="id">label</label>',
            R::label("label", "for=id"),
        );
    }

    public function test_select(): void
    {
        $this->assertEquals(
            '<select id="select" name="select"><option value="v1">V1</option><option value="v2" selected="selected">V2</option></select>',
            R::select(
                [
                    'v1' => 'V1',
                    'v2' => 'V2',
                ],
                'v2',
                "id=select&name=select",
            ),
        );
    }

    public function test_textarea(): void
    {
        $this->assertEquals(
            '<textarea id="id" name="name">value</textarea>',
            R::textarea('value', "id=id&name=name"),
        );
    }
}
