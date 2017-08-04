<?php

namespace ClawRock\Slack\Logic\Request;

use ClawRock\Slack\Common\Enum\RequestType;

class SlashRequest extends AbstractRequest
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
     * SlackRequest constructor.
     * @param array $requestData
     */
    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
        $this->requestType = RequestType::SLASH_COMMAND();
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->getRequestData('payload');
    }

    /**
     * @return string
     */
    public function getTeamId()
    {
        return $this->getRequestData('team_id');
    }

    /**
     * @return string
     */
    public function getTeamDomain()
    {
        return $this->getRequestData('team_domain');
    }

    /**
     * @return string
     */
    public function getChannelId()
    {
        return $this->getRequestData('channel_id');
    }

    /**
     * @return string
     */
    public function getChannelName()
    {
        return $this->getRequestData('channel_name');
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->getRequestData('user_id');
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->getRequestData('user_name');
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->getRequestData('command');
    }

    /**
     * @return string
     */
    public function getResponseUrl()
    {
        return $this->getRequestData('response_url');
    }

    public function getCommandParameterString()
    {
        return $this->getText();
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->getRequestData('text');
    }
}
