<?php
declare(strict_types=1);

use Controller\CreateUserController;
use Slim\Factory\AppFactory;
use Model\ConcreteUserRepository; // Antaget at ConcreteUserRepository implementerer UserRepository
use Slim\Psr7\Request;
use Slim\Psr7\Response;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$responseFactory = $app->getResponseFactory();

$app->post('/create-user', function (Request $request, Response $response) use ($responseFactory) {
    $twig = new Twig();
    $userRepository = new ConcreteUserRepository(); // Brug af den konkrete klasse, der implementerer UserRepository
    $createUserController = new CreateUserController($twig, $userRepository);
    return $createUserController->apply($request, $response);
});

$app->run();
