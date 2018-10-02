<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OAuth2Framework\ServerBundle\Tests\TestBundle\Handler;

use OAuth2Framework\Component\AuthorizationEndpoint\AuthorizationRequest\AuthorizationRequest;
use OAuth2Framework\Component\AuthorizationEndpoint\ConsentHandler as ConsentHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class ConsentHandler implements ConsentHandlerInterface
{
    public function prepare(ServerRequestInterface $serverRequest, string $authorizationId, AuthorizationRequest $authorizationRequest): void
    {
    }

    public function hasBeenProcessed(ServerRequestInterface $serverRequest, string $authorizationId, AuthorizationRequest $authorizationRequest): bool
    {
        return true;
    }

    public function process(ServerRequestInterface $serverRequest, string $authorizationId, AuthorizationRequest $authorizationRequest): ResponseInterface
    {
        return new Response('You are on the consent page');
    }
}
