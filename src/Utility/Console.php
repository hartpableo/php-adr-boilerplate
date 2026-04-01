<?php

namespace App\Utility;

class Console {
  private static function colour(string $text, string $code): string {
    return "\n\033[{$code}m{$text}\033[0m\n";
  }

  public static function error(string $message): void {
    echo self::colour("❌ ERROR: {$message}", '31') . PHP_EOL;
  }

  public static function warning(string $message): void {
    echo self::colour("⚠️  WARNING: {$message}", '33') . PHP_EOL;
  }

  public static function success(string $message): void {
    echo self::colour("✅ SUCCESS: {$message}", '32') . PHP_EOL;
  }

  public static function notice(string $message): void {
    echo self::colour("ℹ️  NOTICE: {$message}", '34') . PHP_EOL;
  }

  public static function info(string $message): void {
    echo self::colour("INFO: {$message}", '36') . PHP_EOL;
  }

  public static function table(array $data): void {
    if (empty($data)) {
      echo "No data available.\n";
      return;
    }

    // Ensure we are working with a numerically indexed array of rows
    $rows = array_values($data);

    // Extract headers from first row
    $headers = array_keys($rows[0]);

    // Calculate column widths
    $widths = [];
    foreach ($headers as $header) {
      $widths[$header] = strlen((string)$header);
    }

    foreach ($rows as $row) {
      foreach ($headers as $header) {
        $value = isset($row[$header]) ? (string)$row[$header] : '';
        $widths[$header] = max($widths[$header], strlen($value));
      }
    }

    // Build separator
    $separator = '+';
    foreach ($headers as $header) {
      $separator .= str_repeat('-', $widths[$header] + 2) . '+';
    }
    $separator .= "\n";

    // Print header row
    echo $separator;
    echo '|';
    foreach ($headers as $header) {
      printf(' %-' . $widths[$header] . 's |', $header);
    }
    echo "\n";
    echo $separator;

    // Print data rows
    foreach ($rows as $row) {
      echo '|';
      foreach ($headers as $header) {
        $value = isset($row[$header]) ? (string)$row[$header] : '';
        printf(' %-' . $widths[$header] . 's |', $value);
      }
      echo "\n";
    }

    echo $separator;
  }
}