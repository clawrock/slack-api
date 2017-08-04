<?php

namespace ClawRock\Slack\Test\Fluent\Response;

use ClawRock\Slack\Common\Enum\ResponseType;
use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Logic\Response\Attachment\Attachment;
use ClawRock\Slack\Logic\Response\Attachment\Field;

class ResponseBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_setting_values()
    {
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->setText('text');
        $attachment = new Attachment(['text' => 'text']);
        $attachment->addField(new Field('title', 'value', true));
        $messageDataBuilder->addAttachment($attachment);
        $messageData = $messageDataBuilder->create();
        $this->assertEquals('{"text":"text","attachments":[{"text":"text","fields":{"title":"title","value":"value","short":true}}]}',
            json_encode($messageData));
    }

    public function test_clearing_attachments()
    {
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->addAttachment(new Attachment(['text' => 'text']));
        $messageDataBuilder->clearAttachments();
        $this->assertArrayNotHasKey('attachments', $messageDataBuilder->getData());
    }

    public function test_concatenating_messageData_text()
    {
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->addText('Lorem ')->addText('ipsum.');
        $this->assertArraySubset(['text' => 'Lorem ipsum.'], $messageDataBuilder->getData());
    }

    public function test_setting_fields()
    {
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->setUsername('username')
            ->setEmoji('emoji')
            ->setLinkNames(true)
            ->setParse(true);
        $this->assertArraySubset([
            'username'   => 'username',
            'icon_emoji' => ':emoji:',
            'link_names' => '1',
            'parse'      => 'full'
        ], $messageDataBuilder->getData());
    }

    public function test_fluent_attachment_creation()
    {
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->createAttachment()->setText('Sample text')->end();
        $this->assertArrayHasKey('attachments', $messageDataBuilder->getData());
    }

    public function test_setting_data()
    {
        $data               =
            ['text' => 'text', 'attachments' => new Attachment(['text' => 'text', 'color' => '#123'])];
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->setData($data);
        $this->assertEquals($messageDataBuilder->getData(), $data);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_setting_non_array_data_throws_exception()
    {
        $data               = 123;
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->setData($data);
    }

    public function test_merging_data_array_priority()
    {
        $array              = ['text' => 'array text'];
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->setText('builder text');
        $messageDataBuilder->mergeData($array, false);
        $this->assertContains('array text builder text', $messageDataBuilder->getData());
    }

    public function test_merging_data_message_builder_priority()
    {
        $array              = ['text' => 'array text'];
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->setText('builder text');
        $messageDataBuilder->mergeData($array, true);
        $this->assertContains('builder text array text', $messageDataBuilder->getData());
    }

    public function test_merging_two_data_builders_priority()
    {
        $messageDataBuilder1 = new ResponseBuilder();
        $messageDataBuilder1->setText('builder1 text');

        $messageDataBuilder2 = new ResponseBuilder();
        $messageDataBuilder2->setText('builder2 text');

        $messageDataBuilder1->mergeDataBuilder($messageDataBuilder2, false);
        $this->assertContains('builder2 text builder1 text', $messageDataBuilder1->getData());
    }

    public function test_merging_data_without_concat()
    {
        $messageDataBuilder1 = new ResponseBuilder();
        $messageDataBuilder1->setText('builder1 text');

        $messageDataBuilder2 = new ResponseBuilder();
        $messageDataBuilder2->setText('builder2 text');

        $messageDataBuilder1->mergeDataBuilder($messageDataBuilder2, false, false);
        $this->assertContains('builder2 text', $messageDataBuilder1->getData());
    }

    public function test_setting_message_type_on_databuilder()
    {
        $messageDataBuilder1 = new ResponseBuilder();
        $messageDataBuilder1->setResponseType(ResponseType::EPHEMERAL());
        $response1 = $messageDataBuilder1->create()->toResponse();

        $messageDataBuilder1->setResponseType(ResponseType::IN_CHANNEL());
        $response2 = $messageDataBuilder1->create()->toResponse();

        $this->assertEquals(ResponseType::EPHEMERAL,$response1->getResponseType());
        $this->assertEquals(ResponseType::IN_CHANNEL, $response2->getResponseType());
    }
}
