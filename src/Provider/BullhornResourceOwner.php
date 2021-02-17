<?php

namespace Jiminny\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Resource owner oin Bullhorn.
 */
class BullhornResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response.
     *
     * @var array
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @throws IdentityProviderException
     */
    public function __construct(array $response)
    {
        if (!isset($response['userId'])) {
            throw new IdentityProviderException('Invalid resource owner response', 0, $response);
        }

        $this->response = $response;
    }

    /**
     * Returns the user id.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->response['userId'];
    }

    /**
     * Returns all of the owner details available as an array.
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
