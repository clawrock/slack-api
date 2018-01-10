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
     * @param Header $header
     * @param string|array $content
     */
    public function __construct(Header $header, $content)
    {
        if (!is_string($content) && !is_array($content)) {
            throw new \InvalidArgumentException('Parameter must be a string or array, ' . gettype($content) . ' provided');
        }
        $this->data = [
            'header'  => $header->getValue(),
            'method'  => 'POST',
            'content' => $content,
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
     * @return string
     */
    public function getContentString()
    {
        $content = $this->getContent();
        return is_string($content) ? $content : http_build_query($content);
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
     * Returns array wrapped in 'http' key and content query
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'http' => [
                'header'  => $this->getHeader(),
                'method'  => $this->getMethod(),
                'content' => $this->getContentString(),
            ],
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
     * @return string|array
     */
    public function getContent()
    {
        return $this->data['content'];
    }
}
