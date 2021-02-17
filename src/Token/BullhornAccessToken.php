<?php

namespace Jiminny\OAuth2\Client\Token;

use League\OAuth2\Client\Token\AccessToken;

/**
 * Custom token that contains the API's base URL.
 */
class BullhornAccessToken extends AccessToken
{
    /**
     * The instance URL.
     *
     * @var string
     */
    private $restUrl;

    public function __construct(array $options = [])
    {
        if (!isset($options['restUrl'])) {
            throw new \InvalidArgumentException('Required option restUrl is missing');
        }

        parent::__construct($options);

        $this->restUrl = $options['restUrl'];
    }

    /**
     * Returns the base URL of the API instance.
     */
    public function getRestUrl(): string
    {
        return $this->restUrl;
    }
}
