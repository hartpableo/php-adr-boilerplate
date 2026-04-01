<?php

namespace App\Responder;

use App\Domain\User\UserEntity;

final class UserResponder
{
  public function __invoke(array $users): void
  {
    // Inspect Accept header here to switch HTML vs JSON if needed.
    header('Content-Type: application/json');
    http_response_code(200);

    echo json_encode(
      array_map(
        fn(UserEntity $u) => [
          'id'    => $u->id,
          'name'  => $u->name,
          'email' => $u->email,
        ],
        $users
      )
    );
  }
}