<?php

namespace ClawRock\Slack\Test\Response;

use ClawRock\Slack\Logic\Response\Attachment\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_setting_non_string_title_throws_exception()
    {
        $field = new Field(123, 'string', true);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_setting_non_string_value_throws_exception()
    {
        $field = new Field('string', false, true);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_setting_non_bool_is_short_property_throws_exception()
    {
        $field = new Field('string', 'string', 'string');
    }

    public function test_creating_new_field()
    {
        $field = new Field('string', 'string', true);
        $this->assertInstanceOf('ClawRock\Slack\Logic\Response\Attachment\Field', $field);
    }

    public function test_setting_and_accessing_data()
    {
        $field = new Field('title', 'value', true);
        $this->assertArraySubset(['title' => 'title', 'value' => 'value', 'short' => true], $field->getData());
    }
}
