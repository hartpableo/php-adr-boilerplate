<?php

namespace App\Responder;

use App\Responder\Utility\TwigResponse;

final class LoginResponder {
  public function showForm(array $errors = [], array $input = []): void {
    new TwigResponse('users/login', [
      'errors' => $errors,
      'input' => $input
    ]);
  }

  public function success(): void {
    // todo: add flash message
    header('Location: /');
    exit;
  }
}