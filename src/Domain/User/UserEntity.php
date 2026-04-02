<?php

namespace App\Domain\User;

final readonly class UserEntity {
  public function __construct(
    public ?int   $id,
    public string $name,
  ) {
  }
}