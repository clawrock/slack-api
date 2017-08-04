<?php

namespace ClawRock\Slack\Logic\Command;

use ClawRock\Slack\Common\CommandParameters;
use ClawRock\Slack\Fluent\Response\MessageDataBuilderInterface;
use ClawRock\Slack\Logic\Request\RequestInterface;

class Command extends AbstractCommand
{
    /**
     * @var string
     */
    protected $textLeft = null;
    /**
     * @var string
     */
    protected $matchingWord = null;

    public function __construct()
    {
        $this->container[self::NO_MATCH] = new Container($this, self::NO_MATCH);
    }

    /**
     * @param RequestInterface            $request
     * @param MessageDataBuilderInterface $response
     * @param CommandParameters|null      $params
     * @return mixed
     */
    public function __invoke(
        RequestInterface $request,
        MessageDataBuilderInterface $response,
        CommandParameters $params = null
    ) {
        $this->textLeft = $params ? $params->getTextLeftover() : '';
        $matchingRegex  = $this->getMatchingRegex($request->getCommandParameterString(), $this->textLeft);
        $container      = $this->container[$matchingRegex];
        $commandParams  = new CommandParameters($this->matchingWord, $this->textLeft);
        if (!is_null($this->runAlways)) {
            call_user_func($this->runAlways, $request, $response, $commandParams);
        }
        return $container->runCallables($request, $response, $commandParams);
    }

    /**
     * @param $word
     * @param $textLeft
     * @return string
     */
    protected function getMatchingRegex($word, $textLeft)
    {
        foreach (array_keys($this->container) as $regex) {
            if ($regex == self::NO_MATCH) {
                continue;
            }

            if (preg_match($regex, $word)) {
                $this->textLeft = $this->getRegexMatchLeftover($regex, $word);
                return $regex;
            }

            if (preg_match($regex, $textLeft)) {
                $this->textLeft = $this->getRegexMatchLeftover($regex, $textLeft);
                return $regex;
            }
        }
        return self::NO_MATCH;
    }

    /**
     * Returns text leftover after splitting with preg_split
     *
     * @param $matchingRegexp
     * @param $textLeft
     * @return string
     */
    protected function getRegexMatchLeftover($matchingRegexp, $textLeft)
    {
        if ($textLeft != '') {
            $split = preg_split($matchingRegexp, $textLeft, 2);
            preg_match($matchingRegexp, $textLeft, $match);
            $this->matchingWord = $match[0];
            $this->textLeft     = $split[1];
        }
        return $this->textLeft;
    }

    /**
     * Returns container responsible for provided regex.
     * Creates new container if regex was not found.
     *
     * @param $regex
     * @return mixed
     */
    public function on($regex)
    {
        if (@preg_match($regex, null) === false) {
            $regex = '/^\Q' . $regex . '\E/';
            if (@preg_match($regex, null) === false) {
                throw new \InvalidArgumentException('Provided text is not valid regular expression string');
            }
        }
        if (empty($this->container[$regex])) {
            $container               = new Container($this, $regex);
            $this->container[$regex] = $container;
        }
        return $this->container[$regex];
    }
}
