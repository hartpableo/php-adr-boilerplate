<?php

namespace App\Domain\User;

interface UserRepository {
  /** @return UserEntity[] */
  public function findAll(): array;

  public function findById(int $id): ?UserEntity;
  
  public function findByEmail(string $email): ?UserEntity;

  public function save(UserEntity $user): UserEntity;

  public function delete(int $id): void;
}