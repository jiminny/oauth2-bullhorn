<?php

namespace Jiminny\OAuth2\Client\Helper;

use Jiminny\OAuth2\Client\Token\BhRestToken;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Tool\QueryBuilderTrait;

/**
 * Helper class to perform the API login required by Bullhorn and return the session token.
 */
class BullhornLoginHandler
{
    use QueryBuilderTrait;

    /**
     * @var AbstractProvider
     */
    private $provider;
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(AbstractProvider $provider, string $baseUrl = 'https://rest.bullhornstaffing.com')
    {
        $this->provider = $provider;
        $this->baseUrl  = rtrim($baseUrl, '/');
    }

    /**
     * Returns the session token.
     *
     * @throws IdentityProviderException
     */
    public function getBHRestToken(string $accessToken, ?int $ttl): BhRestToken
    {
        $loginData      = $this->login($accessToken, $ttl);
        $expirationTime = $this->getExpirationTime($loginData['restUrl'], $loginData['BhRestToken']);

        return new BhRestToken($loginData['BhRestToken'], $loginData['restUrl'], $expirationTime);
    }

    /**
     * Performs a login API call and retrieves the BhRestToken.
     *
     * @throws IdentityProviderException
     */
    public function login(string $accessToken, ?int $ttl): array
    {
        $params = [
            'access_token' => $accessToken,
            'version'      => '*',
        ];

        if ($ttl !== null) {
            $params['ttl'] = $ttl;
        }

        $url = sprintf('%s/rest-services/login?%s', $this->baseUrl, $this->buildQueryString($params));

        $request = $this->provider->getRequest($this->provider::METHOD_GET, $url);

        $response = $this->provider->getParsedResponse($request);

        if (!isset($response['BhRestToken'], $response['restUrl'])) {
            throw new IdentityProviderException('Invalid login call response!', 0, $response);
        }

        return $response;
    }

    /**
     * Returns the expiration time for the provided session token.
     *
     * @return int Timestamp
     *
     * @throws IdentityProviderException
     */
    public function getExpirationTime(string $instanceUrl, string $bhRestToken): int
    {
        $url = sprintf('%s/ping?%s', rtrim($instanceUrl, '/'), $this->buildQueryString(['BhRestToken' => $bhRestToken]));

        $request = $this->provider->getRequest($this->provider::METHOD_GET, $url);

        $response = $this->provider->getParsedResponse($request);

        if (!isset($response['sessionExpires']) || $response['sessionExpires'] <= (time() - 1) * 1000) {
            throw new IdentityProviderException('Invalid ping call response!', 0, $response);
        }

        // response provides timestamp with milliseconds.
        return (int) floor($response['sessionExpires'] / 1000);
    }
}
