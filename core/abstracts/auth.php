<?php

namespace MeshMVC;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

abstract class Auth {

    public abstract function getBaseAuthorizationUrl();
    public abstract function getBaseAccessTokenUrl(array $params);
    public abstract function getResourceOwnerDetailsUrl(AccessToken $token);
    protected abstract function getDefaultScopes();
    protected abstract function checkResponse(ResponseInterface $response, $data);
    protected abstract function createResourceOwner(array $response, AccessToken $token);

}
