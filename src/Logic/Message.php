<?php

namespace ClawRock\Slack\Logic;

use ClawRock\Slack\Common\Enum\Header;
use ClawRock\Slack\Common\SendableInterface;
use ClawRock\Slack\Logic\RequestSender\RequestOptions;
use ClawRock\Slack\Logic\RequestSender\RequestSenderInterface;

class Message implements SendableInterface
{
    /**
     * @var RequestSenderInterface
     */
    protected $requestSender;

    /**
     * @var MessageData
     */
    protected $messageData;

    /**
     * @param RequestSenderInterface $requestSender Instance of RequestSenderInterface implementation.
     *                                     Responsible for making Requests to external servers
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
        $this->messageData   = new MessageData();
    }

    /**
     * @param RequestSenderInterface $requestSender
     * @return $this
     */
    public function setRequestSender(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
        return $this;
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
     * @param MessageData|null $messageData
     * @return $this
     */
    public function send(MessageData $messageData = null)
    {
        $message        = is_null($messageData) ? $this->messageData : $messageData;
        $requestOptions = new RequestOptions(Header::JSON(), json_encode($message));
        $this->requestSender->send($requestOptions);
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function sendText($text)
    {
        if (!is_string($text)) {
            throw new \InvalidArgumentException('Parameter must be a string, ' . gettype($text) . ' provided');
        }
        $messageData = new MessageData();
        $messageData->setArgument('text', $text);
        $requestOptions = new RequestOptions(Header::JSON(), json_encode($messageData));
        $this->requestSender->send($requestOptions);
        return $this;
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $code
     * @param string $redirectUrl
     * @return string Json Response.
     */
    public function sendAuthentication($clientId, $clientSecret, $code, $redirectUrl = '')
    {
        $data = [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'code'          => $code
        ];
        if ($redirectUrl != '') {
            $data['redirect_url'] = $redirectUrl;
        }
        $query          = http_build_query($data);
        $requestOptions = new RequestOptions(Header::URL_UNENCODED(), $query);
        $request        = $this->requestSender->send($requestOptions);
        return $request;
    }
}
