<?php

namespace ClawRock\Slack\Test\Fluent\Response;

use ClawRock\Slack\Common\Enum\ActionStyle;
use ClawRock\Slack\Common\Enum\AttachmentColor;
use ClawRock\Slack\Fluent\Response\AttachmentBuilder;
use ClawRock\Slack\Logic\Response\Attachment\Attachment;

class AttachmentBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_setting_values()
    {
        $attachmentBuilder = new AttachmentBuilder();
        $attachmentBuilder->setText('Text')
            ->setAuthorIcon('Icon')
            ->setAuthorLink('http://example.com/')
            ->setAuthorName('Name')
            ->setColor('#111')
            ->setFallback('Fallback')
            ->setFooter('Footer')
            ->setFooterIcon('FooterIcon')
            ->setImageUrl('http://example.com/')
            ->setPretext('Pretext')
            ->setThumbUrl('http://example.com/')
            ->setTitle('Title')
            ->setCallbackId('callback_id')
            ->setTitleLink('http://example.com/')
            ->setTs('Ts');
        $attachment = $attachmentBuilder->create();
        $this->assertArraySubset([
            'text'        => 'Text',
            'author_icon' => ':Icon:',
            'author_link' => 'http://example.com/',
            'author_name' => 'Name',
            'color'       => '#111',
            'fallback'    => 'Fallback',
            'footer'      => 'Footer',
            'footer_icon' => ':FooterIcon:',
            'image_url'   => 'http://example.com/',
            'pretext'     => 'Pretext',
            'thumb_url'   => 'http://example.com/',
            'title'       => 'Title',
            'callback_id' => 'callback_id',
            'title_link'  => 'http://example.com/',
            'ts'          => 'Ts'
        ], $attachment->getData());
    }

    public function test_setting_fields()
    {
        $attachmentBuilder = new AttachmentBuilder();
        $attachment        = $attachmentBuilder->addField('title', 'value', true)->create();
        $fieldData         = $attachment->getData()['fields'][0]->getData();
        $this->assertArraySubset(['title' => 'title', 'value' => 'value', 'short' => true], $fieldData);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_invalid_hex_color_throws_exception()
    {
        $attachmentBuilder = new AttachmentBuilder();
        $attachmentBuilder->setColor('not-hex');
    }

    public function test_setting_color_using_attachment_color_object()
    {
        $attachmentBuilder = new AttachmentBuilder();
        $attachmentBuilder->setColor(AttachmentColor::INDIGO());
        $this->assertContains(AttachmentColor::INDIGO, $attachmentBuilder->getData());
    }

    public function test_build_ending_without_parent()
    {
        $attachmentBuilder = new AttachmentBuilder();
        $attachment        = $attachmentBuilder->end();
        $this->assertInstanceOf(Attachment::class, $attachment);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_setters_throw_exception_on_non_string_non_null_values()
    {
        $attachmentBuilder = new AttachmentBuilder();
        $attachmentBuilder->setText(123);
    }

    public function test_setting_actions()
    {
        $attachmentBuilder = new AttachmentBuilder();
        $attachmentBuilder->createButton()
            ->setName('Sample name')
            ->setValue('Sample val')
            ->setText('Sample text')
            ->setStyle(ActionStyle::DANGER())
            ->setType('button')
            ->end();
        $attachment = $attachmentBuilder->create();
        $actionData = $attachment->getData()['actions'][0]->getData();
        $this->assertArrayHasKey('actions', $attachment->getData());
        $this->assertArraySubset([
            'name'  => 'Sample name',
            'value' => 'Sample val',
            'text'  => 'Sample text',
            'style' => 'danger',
            'type'  => 'button'
        ], $actionData);
    }

}
