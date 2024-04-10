<?php

use controller\CreateUserController;
use controller\FlashController;
use controller\HomeController;
use DI\ContainerBuilder;
use Model\Repository\MySqlUserRepository;
use model\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Slim\Views\TwigMiddleware;
use Student\SlimSkeleton\Model\Repository\PDOSingleton;

// Create container builder
$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

$container->set(ResponseFactoryInterface::class, function () {
    return new ResponseFactory();
});

// Database configuration
$container->set('db_config', [
    'username' => $_ENV['MYSQL_USER'] ?? '',
    'password' => $_ENV['MYSQL_PASSWORD'] ?? '',
    'host' => $_ENV['MYSQL_HOST'] ?? '',
    'port' => $_ENV['MYSQL_PORT'] ?? '',
    'database' => $_ENV['MYSQL_DATABASE'] ?? ''
]);

// Database connection
$container->set('db', function (ContainerInterface $c) {
    $config = $c->get('db_config');
    return PDOSingleton::getInstance(
        $config['username'],
        $config['password'],
        $config['host'],
        $config['port'],
        $config['database']
    );
});

// UserRepository
$container->set(UserRepository::class, function (ContainerInterface $c) {
    $database = $c->get('db');
    return new MySqlUserRepository($database);
});

// Set up Twig middleware in the container
$container->set(TwigMiddleware::class, function (ContainerInterface $c) {
    return TwigMiddleware::createFromContainer($c->get(App::class));
});

// Set up controllers
$container->set(HomeController::class, function (ContainerInterface $c) {
    return new HomeController($c->get(Twig::class), $c->get(Messages::class));
});

$container->set(CreateUserController::class, function (ContainerInterface $c) {
    return new CreateUserController(
        $c->get(UserRepository::class),
        $c->get(ResponseFactoryInterface::class)
    );
});

$container->set(FlashController::class, function (ContainerInterface $c) {
    return new FlashController($c->get(Messages::class), $c->get(ResponseFactoryInterface::class));
});

// Set up Flash service
$container->set(Messages::class, function (ContainerInterface $c) {
    return new Messages();
});

function getCreateUserController(ContainerInterface $container): CreateUserController
{
    return $container->get(CreateUserController::class);
}

return $container;
