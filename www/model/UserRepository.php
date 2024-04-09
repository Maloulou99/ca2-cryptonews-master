<?php

namespace model;

interface UserRepository
{
    public function save(User $user): void;

}