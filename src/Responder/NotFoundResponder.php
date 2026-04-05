<?php

namespace App\Responder;

use App\Responder\Utility\TwigResponse;

class NotFoundResponder {
  public function __invoke() {
    new TwigResponse('status/404', [], 404);
  }
}