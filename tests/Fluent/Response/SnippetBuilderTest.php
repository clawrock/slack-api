<?php

namespace ClawRock\Slack\Test\Fluent\Response;

use ClawRock\Slack\Common\Enum\FileType;
use ClawRock\Slack\Fluent\Response\SnippetBuilder;

class SnippetBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SnippetBuilder
     */
    protected $snippetBuilder;

    /**
     * @var string
     */
    protected $path;

    public function setUp()
    {
        $token                = 'TEST-TOKEN';
        $this->path           = tempnam(sys_get_temp_dir(), 'prefix');
        $this->snippetBuilder = new SnippetBuilder('TEST-TOKEN');
    }

    public function tearDown()
    {
        unlink($this->path);
    }

    public function test_building_content_snippet()
    {
        $snippet = $this->snippetBuilder->setContent('test-content', FileType::GO())->create();
        $options = $snippet->getRequestOptions();
        $this->assertArraySubset(
            [
                'token'    => 'TEST-TOKEN',
                'content'  => 'test-content',
                'filetype' => FileType::GO,
            ],
            $options->getContent()
        );
        $this->assertFalse($this->snippetBuilder->isFileSnippet());
    }

    public function test_building_file_snippet()
    {
        $snippet = $this->snippetBuilder->setFile($this->path)->create();
        $this->assertTrue($this->snippetBuilder->isFileSnippet());
    }

    public function test_setting_file_then_content_will_change_snippet_type()
    {
        $this->snippetBuilder->setFile($this->path);

        $this->assertTrue($this->snippetBuilder->isFileSnippet());

        $this->snippetBuilder->setContent('Test content');

        $this->assertFalse($this->snippetBuilder->isFileSnippet());
    }

    public function test_setting_snippet_data()
    {
        $snippet = $this->snippetBuilder->setContent('Test content')
            ->setDestination(['TestUserId', 'TestChannelId'])
            ->addChannel('TestChannel')
            ->addUser('TestUser')
            ->setInitialComment('Initial comment')
            ->setTitle('Test title')
            ->create();

        $this->assertArraySubset(
            [
                'token'           => 'TEST-TOKEN',
                'channels'        => 'TestUserId,TestChannelId,TestChannel,TestUser',
                'initial_comment' => 'Initial comment',
                'content'         => 'Test content',
                'title'           => 'Test title',
                'filetype'        => 'auto',
            ],
            $snippet->getRequestOptions()->getContent());
    }
}
