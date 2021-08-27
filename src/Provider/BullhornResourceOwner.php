<?php

namespace Jiminny\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Stub resource owner for Bullhorn.
 */
final class BullhornResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response.
     *
     * @var array
     */
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return null;
    }

    public function toArray()
    {
        return $this->response;
    }
}
