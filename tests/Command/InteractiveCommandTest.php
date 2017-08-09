<?php

namespace ClawRock\Slack\Test\Command;

use ClawRock\Slack\Fluent\Response\ResponseBuilder;
use ClawRock\Slack\Logic\Command\InteractiveAnswer\Answer;
use ClawRock\Slack\Logic\Command\InteractiveCommand;
use ClawRock\Slack\Logic\Request\InteractiveRequest;

class InteractiveCommandTest extends \PHPUnit_Framework_TestCase
{
    private $json;
    private $response;

    public function setUp()
    {
        $this->json     =
            ['payload' => '{"actions":[{"name":"war","value":"war"}],"callback_id":"simple_callback","team":{"id":"T0DTKPES3","domain":"clawrock"},"channel":{"id":"G3K8U4QUQ","name":"privategroup"},"user":{"id":"U3KMAQQTT","name":"office"},"action_ts":"1485333856.717065","message_ts":"1485333853.000003","attachment_id":"1","token":"1l2uj9wN0femiGtTZcAmqUDf","response_url":"https:\/\/hooks.slack.com\/actions\/T0DTKPES3\/131465735312\/PxHkAV9fIQn7iaEIqisWa5Co"}'];
        $this->response = new ResponseBuilder();
    }

    public function test_interactive_answers()
    {
        $request = new InteractiveRequest($this->json);
        $answer  = new Answer('love');
        $answer2 = new Answer('war');
        $command = new InteractiveCommand('token');
        $command->on('simple_callback')
            ->run($answer->setRun(function () {
                return 'invalid answer';
            }))
            ->run($answer2->setRun(function () {
                return 'proper answer';
            }));
        $result = $command->__invoke($request, $this->response);
        $this->assertContains('proper answer', $result);
    }
}
