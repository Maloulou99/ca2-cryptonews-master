<?php
declare(strict_types=1);
namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class FlashController
{
    public function __construct(
        Twig $twig,
        Messages $flash
    ) {}

    public function addMessage(Request $request, Response $response): Response
    {

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $response
            ->withHeader('Location', $routeParser->urlFor("home"))
            ->withStatus(302);
    }


}