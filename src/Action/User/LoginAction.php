<?php

namespace App\Action\User;

use App\Domain\User\UserService;
use App\Responder\LoginResponder;

class LoginAction {
  public function __construct(
    private UserService    $service,
    private LoginResponder $responder,
  ) {
  }

  public function __invoke(array $server, array $get, array $post): void {
    if ($server['REQUEST_METHOD'] === 'POST') {
      $this->handlePost($post);
      return;
    }

    $this->responder->showForm();
  }

  private function handlePost(array $post): void {
    $email = $post['email'] ?? '';
    $password = $post['password'] ?? '';

    try {
      $this->service->findByEmail($email, $password);
      $this->responder->success();
    } catch (\InvalidArgumentException $e) {
      $this->responder->showForm(
        ['general' => $e->getMessage()],
        ['email' => $email]
      );
    }
  }
}