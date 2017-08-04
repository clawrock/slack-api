<?php

namespace ClawRock\Slack\Logic\Request;

use ClawRock\Slack\Common\Enum\RequestType;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * @var array
     */
    protected $requestData;

    /**
     * @var RequestType
     */
    protected $requestType;

    /**
     * @return RequestType
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->getRequestData('token');
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getRequestData($key = null)
    {
        if (is_null($key)) {
            return $this->requestData;
        }
        if (is_string($key)) {
            return !empty($this->requestData[$key]) ? $this->requestData[$key] : '';
        }
        throw new \InvalidArgumentException('Parameter must be a string or null, ' . gettype($key) . ' provided.');
    }
}
