<?php

namespace Jiminny\OAuth2\Client\Provider;

use Jiminny\OAuth2\Client\Helper\BullhornLoginHandler;
use Jiminny\OAuth2\Client\Token\BullhornAccessToken;
use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;

class Bullhorn extends AbstractProvider
{
    /**
     * Base URL of Bullhorn API's OAuth endpoints.
     *
     * @var string
     */
    protected $oauthBaseUrl = 'https://auth.bullhornstaffing.com';
    /**
     * The desired TTL of the access token in minutes.
     *
     * @var int
     */
    protected $accessTokenTtl = 180;

    public function getBaseAuthorizationUrl(): string
    {
        return sprintf('%s/oauth/authorize', rtrim($this->oauthBaseUrl, '/'));
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return sprintf('%s/oauth/token', rtrim($this->oauthBaseUrl, '/'));
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        if (!$token instanceof BullhornAccessToken) {
            throw new IdentityProviderException('Invalid access token provided', 0, $token->getValues());
        }

        return sprintf('%s/settings/userId', $token->getRestUrl());
    }

    public function getDefaultTtl(): int
    {
        return $this->accessTokenTtl * 60;
    }

    protected function getDefaultScopes(): array
    {
        return [];
    }

    protected function getAuthorizationHeaders($token = null): array
    {
        if ($token instanceof BullhornAccessToken) {
            return ['BhRestToken' => $token->getToken()];
        }

        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            $errorMessage = $response->getReasonPhrase();
            if (isset($data['error'], $data['error_description']) && is_scalar($data['error']) && is_scalar($data['error_description'])) {
                $errorMessage = sprintf('%s: %s', $data['error'], $data['error_description']);
            } elseif (isset($data['errorMessage']) && is_scalar($data['errorMessage'])) {
                $errorMessage = $data['errorMessage'];
            }

            if (empty($errorMessage)) {
                $errorMessage = 'Unidentified error occurred';
            }

            throw new IdentityProviderException($errorMessage, $statusCode, (string) $response->getBody());
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new BullhornResourceOwner($response);
    }

    protected function getAccessTokenUrl(array $params): string
    {
        // Bullhorn expects query parameters for POST requests as well
        $url = $this->getBaseAccessTokenUrl($params);

        $query = $this->getAccessTokenQuery($params);

        return $this->appendQuery($url, $query);
    }

    protected function createAccessToken(array $response, AbstractGrant $grant): AccessTokenInterface
    {
        return new BullhornAccessToken($response);
    }

    /**
     * Overridden to perform the Bullhorn API login call and replace the OAuth access token with the BhRestToken.
     */
    protected function prepareAccessTokenResponse(array $result): array
    {
        $result = parent::prepareAccessTokenResponse($result);

        $loginHandler = new BullhornLoginHandler($this);
        $bhRestToken  = $loginHandler->getBHRestToken($result['access_token'], $this->accessTokenTtl);

        $result = array_merge($result, [
            'access_token' => $bhRestToken->getToken(),
            'restUrl'      => $bhRestToken->getRestUrl(),
            // convert expiration timestamp to TTL
            'expires_in' => $bhRestToken->getExpiresAt() - time(),
        ]);

        return $result;
    }
}
