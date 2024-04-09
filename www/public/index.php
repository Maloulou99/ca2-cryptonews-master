<?php

declare(strict_types=1);

use Controller\CreateUserController;
use Model\Repository\MySqlUserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../config/dependencies.php";

try {
    // Opret Slim-applikation
    $app = AppFactory::create();

    // Opret en kontainer og konfigurer Twig-visning i den
    $container = $app->getContainer();
    $container->set('view', function () {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Twig($loader);
        return $twig;
    });

    // Tilføj TwigMiddleware til applikationen
    $app->add(TwigMiddleware::createFromContainer($app));

    // Opret forbindelse til databasen og opret brugerrepository
    $database = new PDO('mysql:host=localhost;dbname=my_database', 'username', 'password');
    $userRepository = new MySqlUserRepository($database);

    // Opret instans af CreateUserController og injicér Twig-visningen og brugerrepository
    $createUserController = new CreateUserController($container->get('view'), $userRepository);

    // Definér ruten til oprettelse af brugeren
    $app->post('/create-user', function (Request $request, Response $response) use ($createUserController) {
        return $createUserController->apply($request, $response);
    });

    // Tilføj body-parsing middleware
    $app->addBodyParsingMiddleware();

    // Kør applikationen
    $app->run();
} catch (Throwable $e) {
    // Håndter fejl ved at vise en generisk fejlside eller logge fejlen
    echo 'An error occurred: ' . $e->getMessage();
}
