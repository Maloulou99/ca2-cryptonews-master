<?php

declare(strict_types=1);

namespace controller;

use Exception;
use Model\User;
use Model\UserRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CreateUserController
{
    private UserRepository $userRepository;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(UserRepository $userRepository, ResponseFactoryInterface $responseFactory)
    {
        $this->userRepository = $userRepository;
        $this->responseFactory = $responseFactory;
    }

    public function apply(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'The email address is not valid.';
        } elseif (strpos($data['email'], '@salle.url.edu') === false) {
            $errors['email'] = 'Only emails from the domain @salle.url.edu are accepted.';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($data['password']) < 7) {
            $errors['password'] = 'The password must contain at least 7 characters.';
        } elseif (!preg_match('/[A-Z]/', $data['password']) || !preg_match('/[a-z]/', $data['password']) || !preg_match('/\d/', $data['password'])) {
            $errors['password'] = 'The password must contain both upper and lower case letters and numbers.';
        }

        if (empty($data['repeat_password']) || $data['password'] !== $data['repeat_password']) {
            $errors['repeat_password'] = 'Passwords do not match.';
        }

        if (isset($data['numBitcoins'])) {
            if (!is_numeric($data['numBitcoins'])) {
                $errors['numBitcoins'] = 'The number of Bitcoins is not a valid number.';
            } elseif ($data['numBitcoins'] < 0 || $data['numBitcoins'] > 40000) {
                $errors['numBitcoins'] = 'Sorry, the number of Bitcoins is either below or above the limits.';
            }
        }

        if (!empty($errors)) {
            $responseBody = [
                'errors' => $errors,
                'data' => $data
            ];
            $response = $this->responseFactory->createResponse(400);
            $response->getBody()->write(json_encode($responseBody));
            return $response;
        }

        $user = new User($data['email'], $data['password'], $data['numBitcoins']);

        try {
            // Save the user using the repository
            $this->userRepository->save($user);
        } catch (Exception $e) {
            $errors['database'] = 'An error occurred while saving user data.';
            $responseBody = [
                'errors' => $errors,
                'data' => $data
            ];

            $response = $this->responseFactory->createResponse(500);
            $response->getBody()->write(json_encode($responseBody));
            return $response;
        }

        $userData = [
            'email' => $user->getEmail(),
            'numBitcoins' => $user->getCoins(),
        ];
        $responseBody = [
            'message' => 'User created successfully',
            'data' => $userData
        ];
        $response = $this->responseFactory->createResponse(201);
        $response->getBody()->write(json_encode($responseBody));
        return $response;
    }
}