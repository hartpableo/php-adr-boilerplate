<?php

namespace App\Action\User;

use App\Domain\User\UserService;
use App\Responder\LogoutResponder;

final class LogoutAction {
  public function __construct(
    private UserService     $service,
    private LogoutResponder $responder,
  ) {
  }

  public function __invoke(array $server, array $get, array $post): void {
    ($this->responder)();
  }
}