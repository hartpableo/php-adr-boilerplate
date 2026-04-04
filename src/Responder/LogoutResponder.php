<?php

namespace App\Responder;

use App\Infrastructure\Session\Flash;
use App\Infrastructure\Session\Session;

final class LogoutResponder {
  public function __invoke(): void {
    if (empty(getUser())) {
      redirect('/login');
    }

    Session::logout();
    Flash::add('success', 'You have been logged out.');
    redirect('/login');
  }
}