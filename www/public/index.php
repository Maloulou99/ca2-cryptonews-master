<?php
declare(strict_types=1);

use controller\CreateUserController;
use DI\ContainerBuilder;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, false);


$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

$container->set('view', function () {
    return Twig::create('/var/www/public/tmp');
});

$app = AppFactory::createFromContainer($container);

$app->add(TwigMiddleware::createFromContainer($app));

$app->get('/index', function ($request, $response) {
    return $this->get('view')->render($response, 'index.twig', ['name' => 'World']);
});

$app->get('/sign-up', function ($request, $response) {
    return $this->get('view')->render($response, 'sign-up.twig');
});

$app->post('/sign-up', function (Request $request, Response $response) use ($container) {
    $createUserController = $container->get(CreateUserController::class);
    return $createUserController->apply($request, $response);
});

$app->get('/sign-in', function (Request $request, Response $response) {
    return $this->get('view')->render($response, 'sign-in.twig');
});

$app->post('/sign-in', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    $errors = [];
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address';
    }
    if (empty($data['password']) || empty($data['repeat_password']) || $data['password'] !== $data['repeat_password']) {
        $errors['password'] = 'Passwords do not match';
    }
    if (!empty($errors)) {
        return $this->get('view')->render($response, 'sign-in.twig', ['errors' => $errors]);
    }
    return $response->withHeader('Location', '/')->withStatus(302);
});

$app->get('/', function ($request, $response) {
    //TODO display a personalized welcome message with the user's username
    return $this->get('view')->render($response, 'home.twig');
});

$app->get('/news', function ($request, $response) {
    return $this->get('view')->render($response, 'news.twig');
});

$app->get('/mkt', function ($request, $response) {
    return $this->get('view')->render($response, 'market.twig');
});

$app->run();

