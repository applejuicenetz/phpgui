<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\Html;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\ViewData;

final class StartController extends Controller
{
    public function handle(): Page
    {
        $servers = $this->server();
        $info = $this->information();
        $net = $servers->netstats;
        $coreinfo = $servers->core->getcoreversion();
        $settings = $this->settings();

        $_SESSION['phpaj']['core_source_ip'] = $servers->server_xml['NETWORKINFO'] ? ($this->networkIp($servers)) : '';
        if (empty($_SESSION['phpaj']['core_source_port'])) {
            $_SESSION['phpaj']['core_source_port'] = $settings['port'] ?? '';
        }

        return new Page('pages/start', [
            'server_name' => $net['servername'],
            'welcome' => $net['welcome'],
            'downloads' => ViewData::downloadCounts(ViewData::downloads()),
            'uploads_active' => (int)($this->uploadsActive()),
            'credits' => Format::bytes($info['CREDITS']),
            'credits_negative' => (float)$info['CREDITS'] < 0,
            'share' => $this->shareSummary(),
            'server_time' => date('d.m.Y - H:i:s', (int)((float)$servers->server_xml['TIME']['VALUES']['CDATA'] / 1000)),
            'core_version' => $coreinfo['VERSION'],
            'core_os' => $coreinfo['SYSTEM'],
            'core_os_icon' => Icons::osByName($coreinfo['SYSTEM']),
            'connected_since' => $this->duration($net['timeconnected']),
            'connections' => $info['OPENCONNECTIONS'] ?? '',
            'session_dl' => Format::bytes($info['SESSIONDOWNLOAD']),
            'session_ul' => Format::bytes($info['SESSIONUPLOAD']),
            'dl_speed' => Format::speed($info['DOWNLOADSPEED']),
            'ul_speed' => Format::speed($info['UPLOADSPEED']),
            'public_ip' => $this->networkIp($servers),
            'users' => $net['users'],
            'filecount' => number_format((float)$net['filecount'], 0, ',', '.'),
            'filesize' => Format::bytes($net['filesize']),
            'news' => !empty($_ENV['GUI_SHOW_NEWS']) ? $this->news((string)$coreinfo['VERSION']) : '',
            'new_version_text' => null,
        ], poll: ['dashboard'], scripts: ['dashboard.js']);
    }

    private function networkIp($servers): string
    {
        $keys = array_keys($servers->server_xml['NETWORKINFO']);

        return (string)($servers->server_xml['NETWORKINFO'][$keys[0]]['IP'] ?? 'n/a');
    }

    private function uploadsActive(): int
    {
        $u = new \appleJuiceNETZ\appleJuice\Uploads();
        $u->refresh_cache();

        return (int)($u->cache['phpaj_ul'] ?? 0);
    }

    /** @return ?array{size:string,count:string} */
    private function shareSummary(): ?array
    {
        if (empty($_ENV['GUI_SHOW_SHARE'])) {
            return null;
        }
        $share = new Share();
        $summary = $share->summary();

        return ['size' => Format::bytes($summary['size']), 'count' => number_format($summary['count'], 0, ',', '.')];
    }

    private function duration(mixed $seconds): string
    {
        if (!is_numeric($seconds)) {
            return '?';
        }
        $s = (int)$seconds;

        return sprintf('%dh %dmin', intdiv($s, 3600), intdiv($s % 3600, 60));
    }

    /** Dashboard-News: kurzer Fremd-HTML-Inhalt, bereinigt und im UTF-8-Format. */
    private function news(string $version): string
    {
        $cache = $_SESSION['phpaj']['news'] ?? null;
        if (is_array($cache) && ($cache['version'] ?? '') === $version && ($cache['time'] ?? 0) > time() - 3600) {
            return $cache['html'];
        }
        $body = @file_get_contents(
            sprintf($_ENV['NEWS_URL'], $version ?: '404'),
            false,
            stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]])
        );
        $html = '';
        if (is_string($body)) {
            $body = Html::toUtf8($body);
            if (preg_match('~<body\b[^>]*>(.*?)</body\s*>~is', $body, $m) === 1) {
                $body = $m[1];
            }
            $html = Html::sanitize($body);
        }
        $_SESSION['phpaj']['news'] = ['version' => $version, 'time' => time(), 'html' => $html];

        return $html;
    }
}
