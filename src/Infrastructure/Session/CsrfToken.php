<?php

namespace App\Infrastructure\Session;

final class CsrfToken {

  /**
   * Generate a new token
   */
  public function generateToken(): string {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
  }

  /**
   * Generate hidden token form field
   */
  public function tokenHiddenField(): string {
    $token = $_SESSION['csrf_token'] ?? $this->generateToken();
    return trim(<<<HTML
        <input type="hidden" name="csrf_token" value="{$token}">
      HTML
    );
  }

  /**
   * Render CSRF token global JS variable if available
   */
  public static function frontend(): string {
    $token = $_SESSION['csrf_token'] ?? NULL;

    if (empty($token)) {
      return '';
    }

    return trim(<<<HTML
        <script>
          var CSRF_TOKEN = "$token";
        </script>
      HTML
    );
  }

  /**
   * Validate token
   */
  public function validateToken(?string $token = NULL): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      error_log('Session not started');
      return FALSE;
    }

    $sessionToken = $_SESSION['csrf_token'] ?? NULL;
    if (!is_string($sessionToken) || $sessionToken === '') {
      error_log('No CSRF token in session');
      return FALSE;
    }

    $token = $token ?? $this->getTokenFromRequest();

    if (!is_string($token) || $token === '') {
      error_log('No CSRF token provided');
      return FALSE;
    }

    $ok = hash_equals($sessionToken, $token);

    if ($ok) {
      unset($_SESSION['csrf_token']);
    }

    return $ok;
  }

  public function rotateToken(): string {
    unset($_SESSION['csrf_token']);
    return $this->generateToken();
  }

  private function getTokenFromRequest(): ?string {
    $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? NULL;
    if (is_string($headerToken) && $headerToken !== '') {
      return $headerToken;
    }

    $postToken = $_POST['csrf_token'] ?? NULL;
    if (is_string($postToken) && $postToken !== '') {
      return $postToken;
    }

    return NULL;
  }
}