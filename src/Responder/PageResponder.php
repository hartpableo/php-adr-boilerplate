<?php

namespace App\Responder;

use App\Responder\Utility\TwigResponse;

final class PageResponder {
  public function home(): void {
    new TwigResponse('pages/home');
  }
}