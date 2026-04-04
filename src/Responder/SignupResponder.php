<?php

namespace App\Responder;

use App\Infrastructure\Session\CsrfToken;
use App\Infrastructure\Session\Flash;
use App\Responder\Utility\TwigResponse;
use Twig\Markup;

final class SignupResponder {
  public function showForm(array $errors = [], array $input = []): void {
    new TwigResponse('users/signup', [
      'errors' => $errors,
      'input' => $input,
      'csrf_token' => new Markup(
        (new CsrfToken())->tokenHiddenField(),
        'UTF-8'
      )
    ]);
  }

  public function success(): void {
    Flash::add('success', 'You have successfully signed up.');
    header('Location: /login');
    exit;
  }
}