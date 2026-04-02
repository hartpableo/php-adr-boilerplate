<?php

namespace App\Responder;

use App\Responder\Utility\TwigResponse;

final class SignupResponder {
  public function showForm(array $errors = [], array $input = []): void {
    new TwigResponse('users/signup', [
      'errors' => $errors,
      'input' => $input
    ]);
  }

  public function success(): void {
    // todo: add flash message
    header('Location: /login');
    exit;
  }
}