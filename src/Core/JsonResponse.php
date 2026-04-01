<?php

namespace App\Core;

use JsonException;

final class JsonResponse {
  private mixed $data;
  private int $statusCode;
  /** @var array<string, string> */
  private array $headers;
  private int $jsonFlags;

  /**
   * @param array<string, string> $headers
   */
  public function __construct(
    mixed $data,
    int   $statusCode = 200,
    array $headers = [],
    int   $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
  ) {
    $this->data = $data;
    $this->statusCode = $statusCode;
    $this->headers = $headers;
    $this->jsonFlags = $jsonFlags;
    $this->send();
  }

  private function send(): void {
    http_response_code($this->statusCode);

    header('Content-Type: application/json; charset=UTF-8');
    foreach ($this->headers as $name => $value) {
      header($name . ': ' . $value);
    }

    try {
      echo json_encode($this->data, $this->jsonFlags | JSON_THROW_ON_ERROR);
    } catch (JsonException) {
      // Fallback: avoid breaking the response completely if encoding fails
      http_response_code(500);
      echo json_encode([
        'ok' => FALSE,
        'message' => 'Failed to encode JSON response'
      ]);
    }

    exit;
  }
}