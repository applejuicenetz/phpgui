<?php

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\Kernel;

class GUI
{
    public function getDeviceConfig(): array
    {
        $cfg = $_COOKIE['cfg'] ?? null;

        if ($cfg) {
            try {
                $cfg = json_decode($cfg, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $cfg = null;
            }
        }

        return $cfg ?? [];
    }

    public function setDeviceConfig(array $cfg): bool
    {
        return setcookie('cfg', json_encode($cfg), time() + 60 * 60 * 24 * 365, '/');
    }

    function check_version($var): void
    {
        $language = Kernel::getLanguage();
        $lang = $language->translate();
        $template = new template();

        $akt_ver = file($_ENV['CHANGELOG_URL']);

        foreach ($akt_ver as $line_num => $line) {
            if ($line_num == 4) {
                $version = str_replace("#", "", $line);

                $version = trim($version);

                $_SESSION['phpaj']['akt_version'] = $version;
            }

        }

        if (version_compare($_SESSION['phpaj']['akt_version'], PHP_GUI_VERSION, '>')) {
            $template->alert("warning", $lang->System->version_1, $lang->System->version_akt . $_SESSION['phpaj']['akt_version']);
        }
    }

    function versions_update($var)
    {
        $akt_ver = file($_ENV['CHANGELOG_URL']);

        foreach ($akt_ver as $line_num => $line) {
            if ($line_num == 4) {
                $version = str_replace("#", "", $line);

                $version = trim($version);

                $_SESSION['phpaj']['akt_version'] = $version;
            }

        }

        if (version_compare($_SESSION['phpaj']['akt_version'], PHP_GUI_VERSION, '>')) {
            return '<span class="col-danger font-bold">' . PHP_GUI_VERSION . '</span>';
        } else {
            return PHP_GUI_VERSION;
        }
    }


}
