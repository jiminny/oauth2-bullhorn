<?php

namespace Jiminny\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Bullhorn extends AbstractProvider
{
    /**
     * Base URL of Bullhorn API's OAuth endpoints.
     *
     * @var string
     */
    protected $oauthBaseUrl = 'https://auth.bullhornstaffing.com';

    public function getBaseAuthorizationUrl(): string
    {
        return sprintf('%s/oauth/authorize', rtrim($this->oauthBaseUrl, '/'));
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return sprintf('%s/oauth/token', rtrim($this->oauthBaseUrl, '/'));
    }

    /**
     * Bullhorn provides no access to such information at this stage, so we will just override it
     */
    protected function fetchResourceOwnerDetails(AccessToken $token): array
    {
        return [];
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): ?string
    {
        return null;
    }

    protected function getDefaultScopes(): array
    {
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
}
