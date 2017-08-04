<?php

namespace ClawRock\Slack\Test\Response;

use ClawRock\Slack\Common\Enum\ResponseType;
use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Logic\MessageData;
use ClawRock\Slack\Logic\Response\Attachment\Attachment;
use ClawRock\Slack\Logic\Response\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function test_setting_response_type()
    {
        $response = new Response(new MessageData());
        $response->setResponseType(ResponseType::IN_CHANNEL());
        $this->assertEquals('in_channel', $response->getResponseType());
    }

    public function test_serializing_data()
    {
        $messageDataBuilder = new ResponseBuilder();
        $messageDataBuilder->addText('test');
        $messageDataBuilder->createAttachment()->addField('Title', 'Test', true)->end();
        $messageData = $messageDataBuilder->create();
        $response    = new Response($messageData);
        $this->assertEquals('{"text":"test","attachments":[{"fallback":"Default fallback message","callback_id":"default_callback","fields":[{"title":"Title","value":"Test","short":true}]}],"response_type":"ephemeral"}',
            json_encode($response));
    }

    public function test_responding()
    {
        $messageData = new MessageData();
        $messageData->setArgument('text', 'message text')->addAttachment(new Attachment(['text' => 'attachment text']));
        $response = new Response($messageData);
        $response->serve();
        $this->expectOutputString('{"text":"message text","attachments":[{"text":"attachment text"}],"response_type":"ephemeral"}');
    }
}
