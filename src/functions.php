<?php

/**
 * Get a .env variable
 */
function env(string $key, mixed $default = null): mixed {
  $key = strtoupper(str_replace('.', '_', $key));
  return $_ENV[$key] ?? $default;
}