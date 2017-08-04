<?php

namespace ClawRock\Slack\Logic\Response;

use ClawRock\Slack\Common\Enum\ResponseType;
use ClawRock\Slack\Common\SendableInterface;
use ClawRock\Slack\Logic\MessageData;

class Response implements \JsonSerializable, SendableInterface
{
    /**
     * @var MessageData
     */
    protected $messageData;

    /**
     * Response constructor.
     * @param MessageData $messageData
     */
    public function __construct(MessageData $messageData)
    {
        $this->messageData = $messageData;
        $this->setResponseType(ResponseType::EPHEMERAL());
    }

    /**
     * @param ResponseType $responseType
     * @return $this
     */
    public function setResponseType(ResponseType $responseType)
    {
        $this->messageData->setArgument('response_type', $responseType->getValue());
        return $this;
    }

    /**
     * Returns type of response.
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->messageData->getArgument('response_type');
    }

    /**
     * Serves the response
     *
     * @return $this
     */
    public function serve()
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode($this);
        return $this;
    }

    /**
     * Serve method alias
     *
     * @return Response
     */
    public function send()
    {
        return $this->serve();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getMessageData();
    }

    /**
     * @return MessageData
     */
    public function getMessageData()
    {
        return $this->messageData;
    }

    /**
     * @param MessageData $messageData
     * @return $this
     */
    public function setMessageData(MessageData $messageData)
    {
        $this->messageData = $messageData;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->getMessageData());
    }
}
