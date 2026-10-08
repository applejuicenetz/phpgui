<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Ergebnis eines Controllers: Template, Daten und Seiteneinstellungen. */
final class Page
{
    /**
     * @param array<string,mixed> $data
     * @param list<string> $scripts JS-Module unter assets/js/
     * @param list<string> $poll Datentypen für das Live-Polling (api.php)
     */
    public function __construct(
        public readonly string $template,
        public readonly array  $data = [],
        public readonly string $title = '',
        public readonly array  $scripts = [],
        public readonly array  $poll = [],
        public readonly string $layout = 'app',
        public readonly int    $status = 200,
    ) {
    }
}
