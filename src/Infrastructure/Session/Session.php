<?php


namespace App\Infrastructure\Session;

final class Session {
  private static array $fieldErrors = [];

  /**
   * Get a session variable
   */
  public static function get(string $key): mixed {
    if ($key === '') {
      return NULL;
    }

    $parts = array_values(array_filter(explode('.', $key), static fn($p) => $p !== ''));

    if ($parts === []) {
      return NULL;
    }

    $ref = $_SESSION;

    $lastIndex = count($parts) - 1;
    foreach ($parts as $i => $part) {
      if (!is_array($ref) || !array_key_exists($part, $ref)) {
        return NULL;
      }

      if ($i === $lastIndex) {
        return $ref[$part];
      }

      $ref = $ref[$part];
    }

    return NULL;
  }

  /**
   * Set a session variable
   */
  public static function set(string $key, mixed $value): void {
    if ($key === '') {
      return;
    }

    $parts = array_values(array_filter(explode('.', $key), static fn($p) => $p !== ''));

    if ($parts === []) {
      return;
    }

    $ref = &$_SESSION;

    $lastIndex = count($parts) - 1;
    foreach ($parts as $i => $part) {
      if ($i === $lastIndex) {
        $ref[$part] = $value;
        return;
      }

      if (!isset($ref[$part]) || !is_array($ref[$part])) {
        $ref[$part] = [];
      }

      $ref = &$ref[$part];
    }
  }

  /**
   * Remove a session variable
   */
  public static function remove(string|array $key): void {
    if (is_array($key)) {
      foreach ($key as $k) {
        unset($_SESSION[$k]);
      }
    } else {
      unset($_SESSION[$key]);
    }
  }

  /**
   * Regenerate the session ID (recommended after login / privilege changes)
   */
  public static function regenerate(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      return;
    }

    session_regenerate_id(TRUE);
  }

  /**
   * Destroy the session and clear all session data
   */
  public static function logout(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      return;
    }

    Session::remove('user');
    Session::regenerate();

    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => (bool)$params['secure'],
        'httponly' => (bool)$params['httponly'],
        'samesite' => 'Lax',
      ]
    );

    session_destroy();
  }
}