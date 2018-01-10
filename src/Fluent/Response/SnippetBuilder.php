<?php

namespace ClawRock\Slack\Fluent\Response;

use ClawRock\Slack\Common\Enum\FileType;
use ClawRock\Slack\Common\Enum\Header;
use ClawRock\Slack\Logic\RequestSender\FileRequestSender;
use ClawRock\Slack\Logic\RequestSender\RequestOptions;
use ClawRock\Slack\Logic\RequestSender\StreamRequestSender;
use ClawRock\Slack\Logic\Snippet;

class SnippetBuilder extends AbstractBuilder
{
    const FILE_ENDPOINT_URL = 'https://slack.com/api/files.upload';

    /**
     * @var string
     */
    protected $token = '';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    protected $fileType;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * @var string
     */
    protected $comment = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var $destinations
     */
    protected $destinations = [];

    /**
     * @var bool
     */
    protected $isFileSnippet = false;

    /**
     * SnippetBuilder constructor.
     * @param string $token
     */
    public function __construct($token)
    {
        $this->validateType($token, ['string']);
        $this->token    = $token;
        $this->fileType = FileType::AUTO_DETECT_TYPE;
    }

    /**
     * @param string[] $channels
     * @return SnippetBuilder
     */
    public function setDestination(array $destinations)
    {
        foreach ($destinations as $destination) {
            $this->validateType($destination, ['string']);
            $this->addChannel($destination);
        }
        return $this;
    }

    /**
     * @param string $channelId
     * @return $this
     */
    public function addChannel($channelId)
    {
        $this->validateType($channelId, ['string']);
        $this->destinations[] = $channelId;
        return $this;
    }

    /**
     * @param string $userId
     * @return $this
     */
    public function addUser($userId)
    {
        $this->addChannel($userId);
        return $this;
    }

    /**
     * @param string        $content
     * @param FileType|null $fileType
     * @return SnippetBuilder
     */
    public function setContent($content, FileType $fileType = null)
    {
        $this->validateType($content, ['string']);
        $this->content       = $content;
        $this->fileType      = $fileType === null ? FileType::AUTO_DETECT_TYPE : $fileType->getValue();
        $this->isFileSnippet = false;
        return $this;
    }

    /**
     * @param string $filePath
     * @param string $fileName
     * @return SnippetBuilder
     */
    public function setFile($filePath, $fileName = '')
    {
        $this->validateType($filePath, ['string', 'readable']);
        $this->validateType($fileName, ['string']);
        $this->filePath      = $filePath;
        $this->fileName      = $fileName;
        $this->isFileSnippet = true;
        return $this;
    }

    /**
     * @param string $comment
     * @return SnippetBuilder
     */
    public function setInitialComment($comment)
    {
        $this->validateType($comment, ['string']);
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param string $title
     * @return SnippetBuilder
     */
    public function setTitle($title)
    {
        $this->validateType($title, ['string']);
        $this->title = $title;
        return $this;
    }

    public function isFileSnippet()
    {
        return $this->isFileSnippet;
    }

    /**
     * @return Snippet
     */
    public function create()
    {
        $data = [
            'token'           => $this->token,
            'title'           => $this->title,
            'channels'        => implode(',', $this->destinations),
            'initial_comment' => $this->comment,
        ];

        if ($this->isFileSnippet()) {
            $data['filename'] = $this->fileName;
            $requestSender    = new FileRequestSender(static::FILE_ENDPOINT_URL, $this->filePath);
            $data             = $this->cleanSnippetFields($data);
            $requestOptions   = new RequestOptions(Header::MULTIPART(), $data);
            return new Snippet($requestSender, $requestOptions);
        }

        $data['content']  = $this->content;
        $data['filetype'] = $this->fileType;
        $requestSender    = new StreamRequestSender(static::FILE_ENDPOINT_URL);
        $data             = $this->cleanSnippetFields($data);
        $requestOptions   = new RequestOptions(Header::URL_UNENCODED(), $data);
        return new Snippet($requestSender, $requestOptions);
    }

    protected function cleanSnippetFields(array $data)
    {
        return array_filter($data, function ($value) {
            return !empty($value);
        });
    }
}
