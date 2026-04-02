<?php


namespace App\Infrastructure\Session;

final class Flash {
  public static function add(string $handle, $details): void {
    $flashes = Session::get("flash.$handle") ?? [];
    $flashes[] = $details;
    Session::set("flash.$handle", $flashes);
  }

  public static function frontend(): string {
    $flashes = Session::get('flash');
    if ($flashes === NULL) {
      return '';
    }

    $json = json_encode($flashes);
    $cssUri = asset('css/flash.css');
    $jsUri = asset('js/flash.js');
    return trim(<<<HTML
      <link rel="stylesheet" href="{$cssUri}" media="screen">
      <script>
        var FLASHES = {$json};
      </script>
      <script src="{$jsUri}" type="module" defer></script>
    HTML
    );
  }

  public static function get(string $handle): array|null {
    return Session::get("flash.$handle");
  }

  public static function remove(string|array $handle): void {
    $flashes = Session::get('flash');
    if ($flashes === NULL) {
      return;
    }

    // Handle array of handles
    if (is_array($handle)) {
      foreach ($handle as $h) {
        unset($flashes[$h]);
      }
    } else {
      // Handle single handle
      unset($flashes[$handle]);
    }

    Session::set('flash', $flashes);
  }

  public static function clear(): void {
    Session::remove('flash');
  }
}