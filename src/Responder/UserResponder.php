<?php

namespace App\Responder;

use App\Responder\Utility\TwigResponse;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class UserResponder {
  /**
   * @throws RuntimeError
   * @throws SyntaxError
   * @throws LoaderError
   */
  public function __invoke(array $users): void {
    new TwigResponse('users/list', ['users' => $users]);
  }
}