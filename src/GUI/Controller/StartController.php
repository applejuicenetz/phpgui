<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Format;
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
            'server_time' => date('H:i:s', (int)((float)$servers->server_xml['TIME']['VALUES']['CDATA'] / 1000)),
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
            'filecount' => Format::compactCount($net['filecount']),
            'filesize' => Format::bytes($net['filesize']),
            'show_news' => !empty($_ENV['GUI_SHOW_NEWS']),
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
}
