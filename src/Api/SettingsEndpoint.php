<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\CoreSettings;
use appleJuiceNETZ\GUI\Request;

final class SettingsEndpoint extends Endpoint
{
    public function get(): array
    {
        $s = CoreSettings::read(new Core());
        // Explicit whitelist: settings.xml can contain password hashes.
        return ['values' => [
            'tempdir' => (string)($s['temporarydirectory'] ?? ''), 'incdir' => (string)($s['incomingdirectory'] ?? ''),
            'c_port' => (int)($s['port'] ?? 0), 'c_xml_port' => (int)($s['xmlport'] ?? 0), 'nick' => (string)($s['nick'] ?? ''),
            'maxcon' => (int)($s['maxconnections'] ?? 0), 'maxul' => (int)($s['maxupload'] ?? 0),
            'uls' => (int)($s['speedperslot'] ?? 0), 'maxdl' => (int)($s['maxdownload'] ?? 0),
            'conturn' => (int)($s['maxnewconnectionsperturn'] ?? 0), 'maxdlsrc' => (int)($s['maxsourcesperfile'] ?? 0),
            'autoconnect' => ($s['autoconnect'] ?? '') === 'true',
        ]];
    }

    public function post(): array
    {
        $core = new Core();
        switch (Request::str('change')) {
            case 'standard':
                foreach (['c_port', 'c_xml_port'] as $key) {
                    if (Request::int($key) < 1 || Request::int($key) > 65535) throw new ApiException(400, 'invalid_value');
                }
                $values = ['Incomingdirectory' => Request::str('incdir'), 'Temporarydirectory' => Request::str('tempdir'),
                    'Port' => Request::int('c_port'), 'XMLPort' => Request::int('c_xml_port'), 'Nickname' => Request::str('nick')];
                $core->command('function', 'setsettings?' . http_build_query($values));
                break;
            case 'connection':
                $values = [];
                foreach (['maxcon' => 'MaxConnections', 'maxul' => 'MaxUpload', 'uls' => 'Speedperslot', 'maxdl' => 'MaxDownload',
                    'conturn' => 'MaxNewConnectionsPerTurn', 'maxdlsrc' => 'MaxSourcesPerFile'] as $input => $field) {
                    $value = Request::str($input);
                    if (!ctype_digit($value)) throw new ApiException(400, 'invalid_value');
                    $values[$field] = (int)$value;
                }
                $values['AutoConnect'] = Request::str('autoconnect') === 'true' ? 'true' : 'false';
                CoreSettings::saveConnection($core, $values);
                break;
            default:
                throw new ApiException(400, 'unknown_action');
        }
        return ['ok' => true];
    }
}
