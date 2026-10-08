<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Navigationspunkte der Anwendung; eine Quelle für Sidebar, Tab-Bar und Mehr-Menü. */
final class Navigation
{
    /**
     * @return list<array{site:string,icon:string,label:string,primary:bool}>
     */
    public static function items(): array
    {
        $n = Format::lang()->Navigation;

        return [
            ['site' => 'start', 'icon' => 'speedometer2', 'label' => $n->dashboard, 'primary' => true],
            ['site' => 'downloads', 'icon' => 'cloud-download', 'label' => $n->downloads, 'primary' => true],
            ['site' => 'uploads', 'icon' => 'cloud-upload', 'label' => $n->uploads, 'primary' => true],
            ['site' => 'search', 'icon' => 'search', 'label' => $n->search, 'primary' => true],
            ['site' => 'shares', 'icon' => 'share', 'label' => $n->shares, 'primary' => false],
            ['site' => 'server', 'icon' => 'hdd-network', 'label' => $n->server_list, 'primary' => false],
            ['site' => 'settings', 'icon' => 'gear', 'label' => $n->settings, 'primary' => false],
        ];
    }

    /** Seiten, die zu einem Navigationspunkt gehören (für die aktive Markierung). */
    public static function parent(string $site): string
    {
        return match ($site) {
            'dl_users', 'dl_parts' => 'downloads',
            'sharefiles' => 'shares',
            default => $site,
        };
    }
}
