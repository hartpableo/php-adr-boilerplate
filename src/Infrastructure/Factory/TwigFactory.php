<?php

namespace App\Infrastructure\Factory;

use App\Infrastructure\Session\CsrfToken;
use App\Infrastructure\Session\Flash;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Markup;
use Twig\TwigFunction;

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

    self::addFunctions($twig);
    self::addGlobals($twig);

    return $twig;
  }

  private static function addFunctions(Environment $twig): void {
    $twig->addFunction(new TwigFunction('asset', 'asset'));
    $twig->addFunction(new TwigFunction('env', function ($key) {
      return (env('app.env') ?? 'prod') !== 'prod'
        ? time()
        : env($key);
    }));
  }

  private static function addGlobals(Environment $twig): void {
    $twig->addGlobal('base_url', baseUrl());
    $twig->addGlobal('user', getUser());
    $twig->addGlobal('session_frontend', [
      'flash' => new Markup(Flash::frontend(), 'UTF-8'),
      'csrf' => new Markup(CsrfToken::frontend(), 'UTF-8'),
    ]);
  }
}