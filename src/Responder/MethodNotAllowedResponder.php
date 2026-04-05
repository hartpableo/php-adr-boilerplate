<?php

namespace App\Responder;

use App\Responder\Utility\TwigResponse;

class MethodNotAllowedResponder {
  public function __invoke() {
    new TwigResponse('status/405', [], 405);
  }
}