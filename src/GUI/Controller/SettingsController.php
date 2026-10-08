<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;

/** Einstellungen des Core (settings) bzw. des Nicknamens (user_settings). */
final class SettingsController extends Controller
{
    public function handle(): Page
    {
        $this->save();
        $s = $this->settings();
        $_SESSION['phpaj']['core_source_port'] = $s['port'] ?? '';

        $values = [
            'tempdir' => (string)($s['temporarydirectory'] ?? ''),
            'incdir' => (string)($s['incomingdirectory'] ?? ''),
            'port' => (string)($s['port'] ?? ''),
            'xml_port' => (string)($s['xmlport'] ?? ''),
            'nick' => (string)($s['nick'] ?? ''),
            'maxcon' => (string)($s['maxconnections'] ?? ''),
            'maxul' => ((float)($s['maxupload'] ?? 0)) / 1024,
            'uls' => (string)($s['speedperslot'] ?? ''),
            'maxdl' => ((float)($s['maxdownload'] ?? 0)) / 1024,
            'conturn' => (string)($s['maxnewconnectionsperturn'] ?? ''),
            'maxdlsrc' => (string)($s['maxsourcesperfile'] ?? ''),
            'autoconnect' => ($s['autoconnect'] ?? '') === 'true',
        ];

        if ($this->site === 'user_settings') {
            return new Page('pages/user_settings', ['v' => $values], $this->lang->Settings->user_title);
        }

        return new Page('pages/settings', ['v' => $values], scripts: ['settings.js']);
    }

    private function save(): void
    {
        if (!$this->guardPost()) {
            return;
        }
        $lang = $this->lang;
        $self = 'index.php?site=' . $this->site;
        switch (Request::str('change')) {
            case 'standard':
                $q = 'Incomingdirectory=' . urlencode(Request::str('incdir'))
                    . '&Temporarydirectory=' . urlencode(Request::str('tempdir'))
                    . '&Port=' . Request::int('c_port')
                    . '&XMLPort=' . Request::int('c_xml_port')
                    . '&Nickname=' . urlencode(Request::str('nick'));
                $this->core->command('function', 'setsettings?' . $q);
                $this->flash('success', $lang->Settings->alert_save_1, $lang->Settings->get_save . '!');
                break;
            case 'nick':
                $this->core->command('function', 'setsettings?nick=' . urlencode(Request::str('nick')));
                $this->flash('success', $lang->Settings->alert_save_1, $lang->Settings->get_save . '!');
                break;
            case 'connection':
                $ul = (int)floor((float)str_replace(',', '.', Request::str('maxul', '0')) * 1024);
                $dl = (int)floor((float)str_replace(',', '.', Request::str('maxdl', '0')) * 1024);
                $auto = Request::str('autoconnect') === 'true' ? 'true' : 'false';
                $this->core->command('function', 'setsettings?MaxConnections=' . Request::int('maxcon')
                    . '&MaxUpload=' . $ul
                    . '&Speedperslot=' . Request::int('uls')
                    . '&MaxDownload=' . $dl
                    . '&MaxNewConnectionsPerTurn=' . Request::int('conturn')
                    . '&AutoConnect=' . $auto
                    . '&MaxSourcesPerFile=' . Request::int('maxdlsrc'));
                $this->flash('success', $lang->Settings->alert_save_2, $lang->Settings->get_save . '!');
                break;
            default:
                return;
        }
        $this->redirect($self);
    }
}
