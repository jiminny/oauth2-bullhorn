<?php

namespace Jiminny\OAuth2\Client\Token;

/**
 * Value object for Bullhorn's BHRestToken.
 *
 * @see http://bullhorn.github.io/rest-api-docs/index.html#login
 * @see http://bullhorn.github.io/Getting-Started-with-REST/
 */
class BhRestToken
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $restUrl;
    /**
     * @var int
     */
    private $expiresAt;

    /**
     * Constructor.
     *
     * @param string $token     the token returned by the login call
     * @param string $restUrl   The base URL that should be used for rest operations
     * @param int    $expiresAt Token expiration time - timestamp with milliseconds
     */
    public function __construct(string $token, string $restUrl, int $expiresAt)
    {
        $this->token     = $token;
        $this->restUrl   = $restUrl;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Returns the BhRestToken to be used for API authentication.
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Returns the URL of the instance that serves the API requests for the authenticated user.
     */
    public function getRestUrl(): string
    {
        return $this->restUrl;
    }

    /**
     * Returns the expiration timestamp for the token.
     */
    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }
}
