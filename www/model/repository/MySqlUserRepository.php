<?php

namespace Model\Repository;

use PDO;
use Model\User;
use Model\UserRepository;

final class MySqlUserRepository implements UserRepository
{
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function save(User $user): void
    {
        $query = <<<'QUERY'
INSERT INTO user(email, password, coins)
VALUES(:email, :password, :coins)
QUERY;

        $statement = $this->database->prepare($query);

        $email = $user->getEmail();
        $password = $user->getPassword();
        $coins = $user->getCoins();

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('coins', $coins, PDO::PARAM_INT);

        $statement->execute();
    }
}
