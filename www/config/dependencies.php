<?php
declare(strict_types=1);

use Controller\CreateUserController;
use DI\Container;
use Model\Repository\MySqlUserRepository;
use model\UserRepository;
use Psr\Container\ContainerInterface;
use Student\SlimSkeleton\Model\Repository\PDOSingleton;

$container = new Container();

$container->set(
    CreateUserController::class,
    function (Container $c) {
        return new CreateUserController($c->get("view"), $c->get(UserRepository::class));
    }
);

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set(UserRepository::class, function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});