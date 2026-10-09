<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\appleJuice\Core;

/** Erkennt ajfsp://-Links und reicht sie an den Core weiter. */
final class LinkProcessor
{
    private const PATTERNS = [
        // ajfsp://file|name|md5|size/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)/#',
        // ajfsp://server|host|port/
        '#ajfsp://(server)\|([^|]*)\|([\d]{1,5})/#',
        // ajfsp://file|name|md5|size|ip:port/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)\|[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}:[\d]{1,5}/#',
        // ajfsp://file|name|md5|size|ip:port:server:serverport/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)\|[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}:[\d]{1,5}:[^:]*:[\d]{1,5}/#',
    ];

    /** @return list<array{type:string,raw:string,name:string,size:int,host:string,port:int}> */
    public static function parse(string $input): array
    {
        $links = [];
        foreach (self::PATTERNS as $regex) {
            preg_match_all($regex, urldecode($input), $matches, PREG_SET_ORDER);
            foreach ($matches as $m) {
                $links[] = $m[1] === 'file'
                    ? ['type' => 'file', 'raw' => $m[0], 'name' => $m[2], 'size' => (int)$m[4], 'host' => '', 'port' => 0]
                    : ['type' => 'server', 'raw' => $m[0], 'name' => '', 'size' => 0, 'host' => $m[2], 'port' => (int)$m[3]];
            }
        }

        return $links;
    }

    /**
     * Normalizes a target directory below the incoming folder. Returns null for an
     * unusable value: the Core silently ignores paths containing ".." or ":", so they
     * are rejected here instead.
     */
    public static function targetDirectory(string $input): ?string
    {
        $path = trim(str_replace('\\', '/', $input), " \t/");
        if ($path === '') {
            return '';
        }
        if (strlen($path) > 255 || preg_match('/[\x00-\x1f\x7f:]|\.\./', $path) === 1) {
            return null;
        }

        return preg_replace('#/{2,}#', '/', $path);
    }

    /**
     * Schickt alle Links an den Core.
     *
     * @param string $targetDirectory subdirectory below the incoming folder for file links; '' keeps the default
     * @return list<array{link:array<string,mixed>,ok:bool,reply:string}>
     */
    public static function submit(string $input, Core $core, string $targetDirectory = ''): array
    {
        $out = [];
        foreach (self::parse($input) as $link) {
            $query = 'processlink?link=' . urlencode($link['raw']);
            if ($targetDirectory !== '' && $link['type'] === 'file') {
                $query .= '&subdir=' . rawurlencode($targetDirectory);
            }
            $reply = (string)$core->command('function', $query);
            $out[] = ['link' => $link, 'ok' => trim($reply) === 'ok', 'reply' => $reply];
        }

        return $out;
    }
}
