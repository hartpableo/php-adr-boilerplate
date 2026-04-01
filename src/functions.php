<?php

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