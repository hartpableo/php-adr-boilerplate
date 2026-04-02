<?php

namespace App\Domain\User;

final readonly class UserService {
  public function __construct(
    private UserRepository $repository,
  ) {
  }

  public function fetchAll(): array {
    return $this->repository->findAll();
    // Returns UserEntity[]
  }

  public function create(string $name, string $email, string $password): UserEntity {
    if (empty(trim($name))) {
      throw new \InvalidArgumentException('Name is required.');
    }
    
    if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException('A valid email is required.');
    }
    
    if (empty(trim($password)) || strlen($password) < 6) {
      throw new \InvalidArgumentException('Password must be at least 6 characters long.');
    }

    if ($this->repository->findByEmail($email)) {
      throw new \InvalidArgumentException('Email is already registered.');
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // id is null until the repository assigns one
    $user = new UserEntity(NULL, $name, $email, $hashedPassword);
    return $this->repository->save($user);
    // Returns a new UserEntity with the id populated
  }

  public function findByEmail(string $email, string $password): UserEntity {
    $user = $this->repository->findByEmail($email);

    if (!$user) {
      throw new \InvalidArgumentException('Invalid email or password.');
    }

    if (!password_verify($password, $user->password)) {
      throw new \InvalidArgumentException('Invalid email or password.');
    }

    return $user;
  }
}