<?php

namespace ClawRock\Slack\Logic\Command;

use ClawRock\Slack\Common\Enum\RequestType;

class SlashCommand extends Command implements DispatcherCommandInterface
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var RequestType
     */
    protected $allowedRequestType;

    /**
     * SlashCommand constructor.
     * @param string $token
     * @param string $command
     */
    public function __construct($token, $command = '')
    {
        foreach ([$token, $command] as $item) {
            if (!is_string($item)) {
                throw new \InvalidArgumentException(
                    'Parameter must be a string, ' . gettype($item) . ' provided'
                );
            }
        }

        $this->token              = $token;
        $this->command            = $command;
        $this->allowedRequestType = RequestType::SLASH_COMMAND();
        parent::__construct();
    }

    /**
     * @return RequestType
     */
    public function getAllowedRequestType()
    {
        return $this->allowedRequestType;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function getCommand()
    {
        return $this->command;
    }
}
