<?php
namespace Aego\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface;

class Odnoklassniki extends AbstractProvider
{
    /**
     * OAuth URL.
     *
     * @const string
     */
    const BASE_VK_URL = 'https://oauth.vk.com';

    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'uid';

    const API_VERSION = '5.37';

    public $scopes = ['email'];
    public $uidKey = 'uid';
    public $responseType = 'json';

    protected $applicationKey;

    public function getAccessToken($grant = 'authorization_code', $params = [])
    {
        return parent::getAccessToken($grant, $params);
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://www.odnoklassniki.ru/oauth/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'http://api.odnoklassniki.ru/oauth/token.do';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $url = 'http://api.odnoklassniki.ru/fb.do?method=users.getCurrentUser&access_token='.$token;
        $url .= '&application_key='.$this->applicationKey;
        $url .= '&sig=' . md5(
                'application_key=' . $this->applicationKey . 'method=users.getCurrentUser'.
                md5($token . $this->clientSecret)
            );
        return $url;
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [ 'email', 'wall' ];
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $message = $data['error_description'];
            throw new IdentityProviderException($message, 0/*$data['error']*/, $data);
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new OkUser($response);
    }

    /**
     * Get the base Ok URL.
     *
     * @return string
     */
    private function getBaseOkUrl()
    {
        return static::BASE_OK_URL;
    }

    /**
     * Requests resource owner details.
     *
     * @param  AccessToken $token
     * @return mixed
     */
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        $url = $this->getResourceOwnerDetailsUrl($token);
        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        $baseResponse = $this->getResponse($request);
        return $baseResponse;
    }

}
