<?php

namespace Controller;


use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class VisitsController
{
    private $twig;

    public function __construct(Twig $twig)
    {
    }

    public function showVisits(Request $request, Response $response): \Psr\Http\Message\ResponseInterface
    {
        if (empty($_SESSION['counter'])) {
            $_SESSION['counter'] = 1;
        } else {
            $_SESSION['counter']++;
        }

        return $this->twig->render($response, 'visits.twig', [
            'visits' => $_SESSION['counter'],
        ]);
    }


}