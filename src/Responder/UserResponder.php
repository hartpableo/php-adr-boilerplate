<?php

namespace App\Responder;

use App\Core\JsonResponse;

final class UserResponder {
  public function __invoke(array $users): void {
    new JsonResponse([
      'ok' => TRUE,
      'message' => 'Users fetched successfully',
      'users' => []
    ]);
  }
}