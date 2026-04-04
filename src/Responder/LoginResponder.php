<?php

namespace App\Responder;

use App\Infrastructure\Session\CsrfToken;
use App\Infrastructure\Session\Flash;
use App\Responder\Utility\TwigResponse;
use Twig\Markup;

final class LoginResponder {
  public function showForm(array $errors = [], array $input = []): void {
    new TwigResponse('users/login', [
      'errors' => $errors,
      'input' => $input,
      'csrf_token' => new Markup(
        (new CsrfToken())->tokenHiddenField(),
        'UTF-8'
      )
    ]);
  }

  public function success(): void {
    Flash::add('success', 'You are now logged in.');
    header('Location: /');
    exit;
  }
}