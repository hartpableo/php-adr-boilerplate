<?php


namespace App\Infrastructure\Session;

final class RateLimiter {
  public static function set(string $key, int $duration): void {
    Session::set("rate_limiters.{$key}", [
      'time' => time(),
      'duration' => $duration
    ]);
  }

  public static function check(string $key): bool {
    $last = Session::get("rate_limiters.{$key}");
    if (!is_array($last)) {
      return TRUE;
    }

    return (time() - $last['time']) >= $last['duration'];
  }

  public static function clearAllExpired(): void {
    $rateLimiters = Session::get('rate_limiters') ?? [];
    $now = time();
    foreach ($rateLimiters as $key => $value) {
      if ($now - $value['time'] >= $value['duration']) {
        unset($_SESSION['rate_limiters'][$key]);
      }
    }
  }
}