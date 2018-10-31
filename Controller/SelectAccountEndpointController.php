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

namespace OAuth2Framework\ServerBundle\Controller;

use Http\Message\ResponseFactory;
use OAuth2Framework\Component\AuthorizationEndpoint\SelectAccountEndpoint;
use OAuth2Framework\Component\AuthorizationEndpoint\SelectAccountHandler;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

final class SelectAccountEndpointController extends SelectAccountEndpoint
{
    private $router;

    public function __construct(ResponseFactory $responseFactory, SessionInterface $session, SelectAccountHandler $selectAccountHandler, RouterInterface $router)
    {
        parent::__construct($responseFactory, $session, $selectAccountHandler);
        $this->router = $router;
    }

    protected function getRouteFor(string $action, string $authorizationId): string
    {
        return $this->router->generate($action, ['authorization_id' => $authorizationId]);
    }
}