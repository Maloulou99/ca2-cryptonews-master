<?php
declare(strict_types=1);

use controller\CreateUserController;
use controller\HomeController;
use controller\VisitsController;
use DI\ContainerBuilder;
use middleware\AfterMiddleware;
use middleware\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

$app->get('/', HomeController::class . ':apply')->setName('home')->add(AfterMiddleware::class);

$app->post('/sign-up', function (ServerRequestInterface $request, ResponseInterface $response) {
    return $response->withHeader('Location', '/home');
});

$app->post('/sign-in', function (ServerRequestInterface $request, ResponseInterface $response) {
    return $response->withHeader('Location', '/home');
});

$app->get('/home', function (Request $request, Response $response) {
    return $this->get("view")->render($response, "home.twig", ["username" => "Malou"]);
})->setName("home");

$app->get('/sign-up', CreateUserController::class . ':apply')->setName('sign-up');

$app->post('/sign-up', function (Request $request, Response $response) use ($container) {
    $createUserController = getCreateUserController($container);
    return $createUserController->apply($request, $response );
});

$app->get('/sign-in', CreateUserController::class . ':apply')->setName('sign-in');


$app->add(AfterMiddleware::class);

$app->add(SessionMiddleware::class);

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    return $response->withHeader('Location', '/home');
});

$app->addErrorMiddleware(true, true, true);
$app->add(AfterMiddleware::class);

$app->get('/visits', VisitsController::class . ':showVisits')->setName('visits');


