<?php

namespace App\Infrastructure\Factory;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

final class TwigFactory {
  public static function create(): Environment {
    $loader = new FilesystemLoader(__DIR__ . '/../../../templates');
    $isDev = (env('app.env') ?? 'prod') !== 'prod';

    $twig = new Environment($loader, [
      'debug' => $isDev,
      'cache' => $isDev ? FALSE : __DIR__ . '/../../../var/cache/twig',
    ]);

    if ($isDev) {
      $twig->addExtension(new DebugExtension());
    }

    return $twig;
  }
}