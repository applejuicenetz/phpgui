<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/**
 * Findet Plugins unter plugins/<name>/info.php. Eine info.php ruft
 * $this->register($name, $icon, $datei) auf; $datei ist relativ zu plugins/.
 */
final class Plugins
{
    /** @var list<array{0:string,1:string,2:string}> */
    public array $liste = [];

    public function Find_Plugins(): void
    {
        $base = GUI_ROOT . '/plugins';
        $dirs = glob($base . '/*/info.php') ?: [];
        sort($dirs);
        foreach ($dirs as $info) {
            include $info;
        }
    }

    public function register(string $name, string $icon, string $filename): void
    {
        $this->liste[] = [$name, $icon, $filename];
    }

    /** Pfad eines registrierten Plugins oder null. */
    public function resolve(string $show): ?string
    {
        $this->Find_Plugins();
        foreach ($this->liste as $plugin) {
            if ($plugin[2] === $show) {
                $path = realpath(GUI_ROOT . '/plugins/' . $show);

                return $path !== false && str_starts_with($path, realpath(GUI_ROOT . '/plugins') . DIRECTORY_SEPARATOR) ? $path : null;
            }
        }

        return null;
    }
}
