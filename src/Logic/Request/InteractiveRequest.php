<?php

namespace ClawRock\Slack\Logic\Request;

use ClawRock\Slack\Common\Enum\Group;
use ClawRock\Slack\Common\Enum\RequestType;
use ClawRock\Slack\Common\Exception\InvalidJsonException;

class InteractiveRequest extends AbstractRequest
{
    /**
     * @var mixed
     */
    protected $requestData;

    /**
     * @var RequestType
     */
    protected $requestType;

    /**
     * InteractiveRequest constructor.
     * @param array $request
     * @throws InvalidJsonException
     * @throws \InvalidArgumentException
     */
    public function __construct(array $request)
    {
        if (empty($request['payload'])) {
            throw new \InvalidArgumentException('Request\'s payload field must be a json string');
        }

        $requestData = $request['payload'];
        $requestData = json_decode($requestData, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new InvalidJsonException('Request\'s payload field contains invalid json object.');
        }
        $this->requestData = $requestData;
        $this->requestType = RequestType::INTERACTIVE_COMMAND();
    }

    /**
     * @return string
     */
    public function getTeamId()
    {
        return $this->getGroupData(Group::TEAM(), 'id');
    }

    /**
     * @param Group       $group
     * @param string|null $key
     */
    public function getGroupData(Group $group, $key)
    {
        if (is_null($key)) {
            return $this->requestData;
        }
        if (is_string($key)) {
            return !empty($this->requestData[$group->getValue()][$key])
                ? $this->requestData[$group->getValue()][$key]
                : '';
        }
        throw new \InvalidArgumentException('Parameter must be a string or null, ' . gettype($key) . ' provided.');
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->getGroupData(Group::USER(), 'id');
    }

    /**
     * @return string
     */
    public function getChannelId()
    {
        return $this->getGroupData(Group::CHANNEL(), 'id');
    }

    /**
     * @return string
     */
    public function getTeamDomain()
    {
        return $this->getGroupData(Group::CHANNEL(), 'domain');
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->getGroupData(Group::USER(), 'name');
    }

    /**
     * @return string
     */
    public function getChannelName()
    {
        return $this->getGroupData(Group::CHANNEL(), 'name');
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->getRequestData('actions');
    }

    /**
     * @return string
     */
    public function getActionTs()
    {
        return $this->getRequestData('action_ts');
    }

    /**
     * @return string
     */
    public function getMessageTs()
    {
        return $this->getRequestData('message_ts');
    }

    /**
     * @return string
     */
    public function getOriginalMessage()
    {
        return $this->getRequestData('original_message');
    }

    /**
     * @return string
     */
    public function getCommandParameterString()
    {
        return $this->getCallbackId();
    }

    /**
     * @return string
     */
    public function getCallbackId()
    {
        return $this->getRequestData('callback_id');
    }
}
