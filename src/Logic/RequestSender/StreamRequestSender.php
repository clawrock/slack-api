<?php

namespace ClawRock\Slack\Logic\RequestSender;

use ClawRock\Slack\Common\Enum\RequestMethod;
use ClawRock\Slack\Common\Exception\NotImplementedException;

class StreamRequestSender implements RequestSenderInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * SlackRequestSender constructor.
     * @param string $url Must be a valid url.
     */
    public function __construct($url)
    {
        $this->setUrl($url);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return StreamRequestSender Returns self instance
     */
    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException($url . ' is not valid URL');
        }
        $this->url = $url;

        return $this;
    }

    /**
     * Sends request using header, method and content provided in $options.
     *
     * TODO: Currently only POST Method is supported, create other methods.
     *
     * @param RequestOptions $options Contains data about headers, method and content
     * @return bool True if a request was successful. Otherwise false
     * @throws NotImplementedException
     */
    public function send(RequestOptions $options)
    {
        if ($options->getMethod() != RequestMethod::POST) {
            throw new NotImplementedException('Currently only the POST method is implemented');
        }
        $context = stream_context_create($options->toArray());
        $result  = @file_get_contents($this->url, false, $context);

        return $result;
    }
}
