<?php

namespace App\Action\User;

use App\Domain\User\UserService;
use App\Responder\UserResponder;

final class ListUsersAction
{
  public function __construct(
    private readonly UserService   $service,
    private readonly UserResponder $responder,
  ) {}

  public function __invoke(array $server, array $get, array $post): void
  {
    // 1. Gather input from $get / $post / $server if needed
    // 2. Invoke the Domain — returns an array of UserEntity objects
    $users = $this->service->fetchAll();

    // 3. Pass result to Responder — that's it
    ($this->responder)($users);
  }
}