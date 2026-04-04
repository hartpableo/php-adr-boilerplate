<?php

use App\Infrastructure\Session\Session;
use JetBrains\PhpStorm\NoReturn;

/**
 * Get a .env variable
 */
function env(string $key, mixed $default = NULL): mixed {
  $key = strtoupper(str_replace('.', '_', $key));
  return $_ENV[$key] ?? $default;
}

/**
 * Root directory
 */
function rootDir(string $path = ''): string {
  return realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . ltrim($path, '/');
}

/**
 * Get the base URL
 */
function baseUrl(string $request_uri = ''): string {
  $request_uri = ltrim($request_uri, '/');
  $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
  return "$protocol://$_SERVER[HTTP_HOST]" . (!empty($request_uri) ? "/$request_uri" : '');
}

/**
 * Asset path
 */
function asset(
  string $path,
  bool   $version = TRUE,
): string {
  $path = ltrim($path, '/');

  return $version
    ? baseUrl("$path?v=" . filemtime(rootDir("public/{$path}")))
    : baseUrl($path);
}

/**
 * Parse CLI parameters
 */
function cliParseParams(array $argv): array {
  $options = [];
  $args = [];

  $count = count($argv);
  for ($i = 1; $i < $count; $i++) {
    $token = $argv[$i];

    // End-of-options marker: everything after is positional
    if ($token === '--') {
      for ($j = $i + 1; $j < $count; $j++) {
        $args[] = $argv[$j];
      }
      break;
    }

    // Long option?
    if (str_starts_with($token, '--') && $token !== '--') {
      $raw = substr($token, 2);

      // --key=value
      $eqPos = strpos($raw, '=');
      if ($eqPos !== FALSE) {
        $key = substr($raw, 0, $eqPos);
        $value = substr($raw, $eqPos + 1);
        $options = cliSetOption($options, $key, $value);
        continue;
      }
    }

    // Positional arg
    $args[] = $token;
  }

  return [
    'options' => $options,
    'args' => $args
  ];
}

/**
 * Set the CLI command option
 */
function cliSetOption(array $options, string $key, mixed $value): array {
  if ($key === '') {
    return $options;
  }

  if (!array_key_exists($key, $options)) {
    $options[$key] = $value;
    return $options;
  }

  // If repeated, collect into array
  if (is_array($options[$key])) {
    $options[$key][] = $value;
    return $options;
  }

  $options[$key] = [$options[$key], $value];
  return $options;
}

/**
 * Get current user
 */
function getUser(): mixed {
  return Session::get('user') ?? NULL;
}

/**
 * Redirect to a URL
 */
#[NoReturn]
function redirect(string $url, $params = []): void {
  if (!empty($params)) {
    $url .= '?' . http_build_query($params);
  }

  header("Location: {$url}");
  exit;
}