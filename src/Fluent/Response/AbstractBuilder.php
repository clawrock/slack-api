<?php

namespace ClawRock\Slack\Fluent\Response;

use ClawRock\Slack\Common\Builder\BuilderInterface;

abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * Validates if the $arg is of type defined in $types array.
     * Available types: string, integer, float, boolean, array, object, null, resource
     *
     * @param       $arg
     * @param array $types
     * @throws \InvalidArgumentException
     */
    protected function validateType($arg, array $types)
    {
        $type  = strtolower(gettype($arg));
        $types = array_map('strtolower', $types);
        if (!in_array($type, $types)) {
            $errorMsg = 'Parameter must be a ' . implode(' ', $types) . ', ' . $type . ' provided';
            throw new \InvalidArgumentException($errorMsg);
        }
    }
}
