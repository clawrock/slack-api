<?php

namespace ClawRock\Slack\Test\Fluent\Response;

use ClawRock\Slack\Common\Enum\ActionStyle;
use ClawRock\Slack\Fluent\Response\Action\ButtonBuilder;

class ButtonBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_setting_fields()
    {
        $actionBuilder = new ButtonBuilder();
        $action        = $actionBuilder->setValue('value')
            ->setText('text')
            ->setName('name')
            ->setStyle(ActionStyle::DANGER())
            ->setConfirm('title', 'text', 'oktext', 'dismisstext')
            ->create();
        $actionData    = $action->getData();
        $this->assertArraySubset([
            'name'    => 'name',
            'type'    => 'button',
            'value'   => 'value',
            'text'    => 'text',
            'style'   => 'danger',
            'confirm' => [
                'title'        => 'title',
                'text'         => 'text',
                'ok_text'      => 'oktext',
                'dismiss_text' => 'dismisstext'
            ]
        ], $actionData);
    }

    public function test_reset_data()
    {
        $actionBuilder = new ButtonBuilder();
        $action        = $actionBuilder->setText('non default text')
            ->reset()
            ->create();
        $this->assertContains('Default name', $action->getData());
        $this->assertNotContains('non default text', $action->getData());
    }
}
