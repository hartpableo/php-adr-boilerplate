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

  public function create(string $name, string $email): UserEntity {
    if (empty(trim($name))) {
      throw new \InvalidArgumentException('Name is required.');
    }

    // id is null until the repository assigns one
    $user = new UserEntity(NULL, $name, $email);
    return $this->repository->save($user);
    // Returns a new UserEntity with the id populated
  }
}