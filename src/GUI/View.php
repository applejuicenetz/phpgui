<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/**
 * Minimale Template-Schicht. Templates liegen unter templates/ und enthalten nur HTML
 * und einfache PHP-Ausgabe. Daten kommen ausschließlich über $data; Ausgaben werden
 * mit e() maskiert.
 */
final class View
{
    /** Gemeinsame Daten, die jedes Template sieht. */
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Rendert templates/<name>.php und gibt den HTML-Code zurück. */
    public static function render(string $name, array $data = []): string
    {
        $file = GUI_ROOT . '/templates/' . $name . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Template nicht gefunden: $name");
        }

        return (static function (string $__file, array $__data): string {
            extract($__data, EXTR_SKIP);
            $e = static fn(mixed $v): string => View::e($v);
            $partial = static fn(string $n, array $d = []): string => View::render('partials/' . $n, $d);
            ob_start();
            try {
                require $__file;
                return (string)ob_get_clean();
            } catch (\Throwable $t) {
                ob_end_clean();
                throw $t;
            }
        })($file, self::$shared + $data);
    }

    public static function icon(string $name, string $class = ''): string
    {
        $cls = trim('icon ' . $class);

        return '<svg class="' . self::e($cls) . '" aria-hidden="true" focusable="false"><use href="#i-' . self::e($name) . '"></use></svg>';
    }

    /** Inline-SVG-Sprite (einmal pro Seite). */
    public static function iconSprite(): string
    {
        $file = GUI_ROOT . '/public/assets/vendor/icons/icons.svg';

        return is_file($file) ? (string)file_get_contents($file) : '';
    }

    /** Asset-URL mit Änderungszeit als Cache-Buster. */
    public static function asset(string $path): string
    {
        $file = GUI_ROOT . '/public/assets/' . $path;
        $v = is_file($file) ? '?v=' . filemtime($file) : '';

        return 'assets/' . $path . $v;
    }
}
