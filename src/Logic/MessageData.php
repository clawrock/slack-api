<?php

namespace ClawRock\Slack\Logic;

use ClawRock\Slack\Common\Enum\ResponseType;
use ClawRock\Slack\Logic\Response\Attachment\Attachment;
use ClawRock\Slack\Logic\Response\Response;
use ClawRock\Slack\SlackFactory;

/**
 * IMPORTANT! Setting data via setContent or setArgument does validate the data.
 *
 * Class MessageData
 * @package ClawRock\Slack\Logic
 */
class MessageData implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $content = [];

    /**
     * @var ResponseType
     */
    protected $responseType;

    public function __construct()
    {
        $this->responseType = ResponseType::EPHEMERAL();
    }

    /**
     * @param bool $withoutNulls
     * @return array
     */
    public function getContent($withoutNulls = true)
    {
        if (boolval($withoutNulls)) {
            return $this->removeNullsRecursive($this->content);
        }
        return $this->content;
    }

    /**
     * IMPORTANT! Does not check if the data is valid.
     *
     * @param array $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param ResponseType $responseType
     * @return $this
     */
    public function setResponseType(ResponseType $responseType)
    {
        $this->responseType = $responseType;
        return $this;
    }

    /**
     * IMPORTANT! Does not check if data is valid.
     *
     * @param $argument
     * @param $value
     * @return $this
     */
    public function setArgument($argument, $value)
    {
        $this->content[$argument] = $value;
        return $this;
    }

    /**
     * @param $argument
     * @return mixed
     */
    public function getArgument($argument)
    {
        return $this->content[$argument];
    }

    /**
     * @param Attachment $attachment
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->content['attachments'][] = $attachment;
    }

    /**
     * @return ResponseType
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * @param $url
     * @return Message
     */
    public function toMessage($url)
    {
        return SlackFactory::getMessageService($url)->setMessageData($this);
    }

    /**
     * @return Response
     */
    public function toResponse()
    {
        $response = new Response($this);
        if (!is_null($this->responseType)) {
            $this->setArgument('response_type', $this->responseType->getValue());
        }
        return $response;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data                  = $this->content;
        $data['response_type'] = $this->responseType->getValue();
        return $data;
    }

    /**
     * @param $array
     * @return mixed
     */
    protected function removeNullsRecursive($array)
    {
        foreach ($array as $key => & $value) {
            if (is_array($value)) {
                $value = $this->removeNullsRecursive($value);
            } else {
                if (!(bool)$value) {
                    unset($array[$key]);
                }
            }
        }
        unset($value);

        return $array;
    }
}
