<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2019 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OAuth2Framework\ServerBundle\Service;

use function Safe\sprintf;
use function Safe\parse_url;
use Base64Url\Base64Url;
use OAuth2Framework\Component\AuthorizationEndpoint\AuthorizationRequest\AuthorizationRequest;
use OAuth2Framework\Component\OpenIdConnect\ConsentScreen\SessionStateParameterExtension as BaseSessionStateParameterExtension;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStateParameterExtension extends BaseSessionStateParameterExtension
{
    private string $storageName;

    private SessionInterface $session;
    private ?string $path;
    private ?string $domain;
    private bool $secure;
    private bool $httpOnly;
    private bool $raw;
    private ?string $sameSite;

    public function __construct(SessionInterface $session, string $storageName, ?string $path = '/', ?string $domain = null, bool $secure = false, bool $httpOnly = true, bool $raw = false, ?string $sameSite = null)
    {
        $this->session = $session;
        $this->storageName = $storageName;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->raw = $raw;
        $this->sameSite = $sameSite;
    }

    protected function getBrowserState(ServerRequestInterface $request, AuthorizationRequest $authorization): string
    {
        if ($this->session->has($this->storageName)) {
            return $this->session->get($this->storageName);
        }

        $browserState = Base64Url::encode(random_bytes(64));
        $this->session->set($this->storageName, $browserState);
        $cookie = new Cookie($this->storageName, $browserState, 0, $this->path, $this->domain, $this->secure, $this->httpOnly, $this->raw, $this->sameSite);
        $authorization->setResponseHeader('Set-Cookie', (string) $cookie);

        return $browserState;
    }

    protected function calculateSessionState(ServerRequestInterface $request, AuthorizationRequest $authorization, string $browserState): string
    {
        $redirectUri = $authorization->getRedirectUri();
        $origin = $this->getOriginUri($redirectUri);
        $salt = Base64Url::encode(random_bytes(16));
        $hash = hash('sha256', sprintf('%s%s%s%s', $authorization->getClient()->getPublicId(), $origin, $browserState, $salt));

        return sprintf('%s.%s', $hash, $salt);
    }

    private function getOriginUri(string $redirectUri): string
    {
        $url_parts = parse_url($redirectUri);

        return sprintf('%s://%s%s', $url_parts['scheme'], $url_parts['host'], isset($url_parts['port']) ? ':'.$url_parts['port'] : '');
    }
}
