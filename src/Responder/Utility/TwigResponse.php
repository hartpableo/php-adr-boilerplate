<?php

namespace App\Responder\Utility;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class TwigResponse {
  private static ?Environment $twigInstance = NULL;

  public static function setTwig(Environment $twig): void {
    self::$twigInstance = $twig;
  }

  /**
   * @throws SyntaxError
   * @throws RuntimeError
   * @throws LoaderError
   */
  public function __construct(
    string $template,
    array  $context = [],
    int    $statusCode = 200,
  ) {
    if (self::$twigInstance === NULL) {
      throw new \RuntimeException('Twig instance not set in TwigResponse.');
    }

    http_response_code($statusCode);
    header('Content-Type: text/html; charset=UTF-8');
    echo self::$twigInstance->render("$template.html.twig", $context);
    exit;
  }
}