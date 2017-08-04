<?php

namespace ClawRock\Slack\Logic\RequestSender;

use ClawRock\Slack\Common\Enum\Header;
use ClawRock\Slack\Common\Enum\RequestMethod;

class RequestOptions
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * RequestOptions constructor.
     * @param string $content
     */
    public function __construct(Header $header, $content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException('Parameter must be a string, ' . gettype($content) . ' provided');
        }
        $this->data = [
            'header'  => $header->getValue(),
            'method'  => 'POST',
            'content' => $content
        ];
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Header $header
     * @return $this
     */
    public function setHeader(Header $header)
    {
        $this->data['header'] = $header->getValue();
        return $this;
    }

    /**
     * @param \JsonSerializable $content
     * @return $this
     */
    public function setContent(\JsonSerializable $content)
    {
        $this->data['content'] = $content;
        return $this;
    }

    /**
     * @param RequestMethod $requestMethod
     * @return $this
     */
    public function setMethod(RequestMethod $requestMethod)
    {
        $this->data['method'] = $requestMethod->getValue();
        return $this;
    }

    /**
     * Returns array wrapped in 'http' key and content encoded in json.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'http' => [
                'header'  => $this->getHeader(),
                'method'  => $this->getMethod(),
                'content' => $this->getContent()
            ]
        ];
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->data['header'];
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->data['method'];
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->data['content'];
    }
}
