<?php

namespace App\Action\Page;

use App\Responder\PageResponder;

class HomePageAction {
  public function __construct(
    private PageResponder $responder,
  ) {
  }

  public function __invoke(array $server, array $get, array $post): void {
    $this->responder->home();
  }
}