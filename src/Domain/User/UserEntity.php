<?php

namespace App\Domain\User;

final class UserEntity {
  public function __construct(
    public readonly ?int   $id,
    public readonly string $name,
    public readonly string $email,
  ) {
  }
}