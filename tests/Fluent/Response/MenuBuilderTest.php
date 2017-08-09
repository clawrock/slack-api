<?php

namespace ClawRock\Slack\Test\Fluent\Response;

use ClawRock\Slack\Common\Enum\MenuSource;
use ClawRock\Slack\Fluent\Response\Action\MenuBuilder;

class MenuBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_setting_fields()
    {
        $actionBuilder = new MenuBuilder();
        $action        = $actionBuilder
            ->setText('text')
            ->setName('name')
            ->addOption('lorem', 'ipsum')
            ->addOption('dolor', 'sit')
            ->create();
        $actionData    = $action->getData();
        $this->assertArraySubset([
            'name'        => 'name',
            'type'        => 'select',
            'text'        => 'text',
            'data_source' => 'static',
            'options'     => [
                [
                    'text'  => 'lorem',
                    'value' => 'ipsum',
                ],
                [
                    'text'  => 'dolor',
                    'value' => 'sit',
                ],
            ],
        ], $actionData);
    }

    public function test_reset_data()
    {
        $actionBuilder = new MenuBuilder();
        $action        = $actionBuilder->setText('non default text')
            ->reset()
            ->create();
        $this->assertContains('Default menu name', $action->getData());
        $this->assertNotContains('non default text', $action->getData());
    }

    public function test_no_options_if_source_not_static()
    {
        $actionBuilder = new MenuBuilder();
        $action        = $actionBuilder->setText('Test menu')
            ->addOption('some', 'option')
            ->setSource(MenuSource::CONVERSATIONS())
            ->create();
        $this->assertArraySubset(['data_source' => MenuSource::CONVERSATIONS], $action->getData());
        $this->assertArrayNotHasKey('options', $action->getData());
    }
}
