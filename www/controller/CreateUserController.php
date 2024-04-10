<?php

declare(strict_types=1);

namespace Controller;

use Exception;
use Model\User;
use Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CreateUserController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function apply(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            $user = new User(
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['coins'] ?? '',
            );

            $this->userRepository->save($user);

            // Redirect to home page or render a success message
            return $response->withHeader('Location', '/home')->withStatus(302);
        } catch (Exception $exception) {
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }
    }
}