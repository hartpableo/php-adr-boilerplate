<?php

namespace App\Responder;

use App\Responder\Utility\TwigResponse;

class NotAuthorizedResponder {
  public function __invoke() {
    new TwigResponse('status/403', [], 403);
  }
}