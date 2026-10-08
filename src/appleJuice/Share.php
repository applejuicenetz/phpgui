<?php

namespace appleJuiceNETZ\appleJuice;

use appleJuiceNETZ\GUI\subs;

class Share
{
    var $core;
    var $dirxml;
    private array $selectedFiles = [];
    private ?array $summary = null;
    private array $objectFiles = [];
    var $separator;
    var $spentprio;
    var $sharemode;

    function __construct()
    {
        $this->core = new Core();
        unset($_SESSION['cache']['SHARE'], $_SESSION['phpaj']['share_LASTTIMESTAMP']);
        $this->separator =& $_SESSION['SEPARATOR'];
        //Um den checkbox-status beim share richtig zu zeigen
        $this->sharemode = array("subdirectory" => "checked",
            "singledirectory" => "");
    }

    function add_share($name, $sharesubs = 0)
    {
        $oldshares = $this->get_shared_dirs();
        $countshares = count($oldshares) + 1;
        $share_args = "countshares=" . $countshares . "&";
        $i = 0;
        foreach ($oldshares as $a) {
            $i++;
            $cur_dir = $this->get_shared_dir($a);
            $share_args .= "sharedirectory" . $i . "=" . urlencode($cur_dir['NAME'])
                . "&sharesub" . $i . "="
                . (($cur_dir['SHAREMODE'] == "subdirectory") ? "True&" : "False&");
        }
        $share_args .= "sharedirectory" . $countshares . "=" . urlencode($name)
            . "&sharesub" . $countshares . "=";
        $share_args .= !empty($sharesubs) ? "True&" : "False&";
        $this->core->command("function", "setsettings?" . $share_args);
    }

    function del_share($name)
    {
        $oldshares = $this->get_shared_dirs();
        $countshares = count($oldshares) - 1;
        $share_args = "countshares=" . $countshares . "&";
        $i = 0;
        foreach ($oldshares as $a) {
            $i++;
            $cur_dir = $this->get_shared_dir($a);
            if ($cur_dir['NAME'] != $name) {
                $share_args .= "sharedirectory$i=" . urlencode($cur_dir['NAME'])
                    . "&sharesub$i="
                    . (($cur_dir['SHAREMODE'] == "subdirectory") ? "True&" : "False&");
            } else {
                $i--;
            }
        }
        $this->core->command("function", "setsettings?" . $share_args);
    }

    function changesub($name, $sharesubs = 0)
    {
        $shares = $this->get_shared_dirs();
        $countshares = count($shares);
        $share_args = "countshares=" . $countshares . "&";
        $i = 0;
        foreach ($shares as $a) {
            $i++;
            $cur_dir = $this->get_shared_dir($a);
            if ($cur_dir['NAME'] != $name) {
                $share_args .= "sharedirectory$i=" . urlencode($cur_dir['NAME'])
                    . "&sharesub$i="
                    . (($cur_dir['SHAREMODE'] == "subdirectory") ? "True&" : "False&");
            } else {
                $share_args .= "sharedirectory$i=" . urlencode($cur_dir['NAME'])
                    . "&sharesub$i=" . (($sharesubs) ? "True&" : "False&");
            }
        }
        $this->core->command("function", "setsettings?" . $share_args);
    }

    function get_temp()
    {
        if (empty($this->dirxml)) $this->get_shared_dirs();
        $tempdirname = $this->dirxml['TEMPORARYDIRECTORY']['VALUES']['CDATA'];
        $tempdirname = substr($tempdirname, 0, strlen($tempdirname) - 1);
        return $tempdirname;
    }

    function get_shared_dirs($force = 0)
    {
        if (empty($this->dirxml) || $force)
            $this->dirxml = $this->core->command("xml", "settings.xml");
        ksort($this->dirxml['SHARE']['VALUES']['DIRECTORY']);    //sortieren
        return array_keys($this->dirxml['SHARE']['VALUES']['DIRECTORY']);
    }

    function get_shared_dir($id)
    {
        return $this->dirxml['SHARE']['VALUES']['DIRECTORY'][$id];
    }

    /** Stream current metadata; no share list is stored in the session. */
    public function scan(callable $consumer): void
    {
        $this->core->command('xml', 'share.xml', '0', $consumer);
    }

    public function refresh_cache($minutes): void
    {
        $this->summary = null;
    }

    public function summary(): array
    {
        if ($this->summary !== null) return $this->summary;
        $summary = ['count' => 0, 'size' => 0, 'spent' => 0];
        $this->scan(static function ($file) use (&$summary): void {
            $summary['count']++;
            $summary['size'] += (float)$file['SIZE'];
            if ((int)$file['PRIORITY'] > 1) $summary['spent'] += (int)$file['PRIORITY'];
        });
        return $this->summary = $summary;
    }

    private function inDirectory(array $file, string $directory): bool
    {
        $separator = str_contains($file['FILENAME'], '\\') ? '\\' : '/';
        $prefix = rtrim($directory, $separator) . $separator;
        return str_starts_with($file['FILENAME'], $prefix)
            && !str_contains(substr($file['FILENAME'], strlen($prefix)), $separator);
    }

    public function page(string $directory, int $page = 1, int $pageSize = 200): array
    {
        $page = max(1, $page);
        $selection = new ShareSelection($page * $pageSize);
        $total = 0;
        $this->spentprio = 0;
        $this->scan(function ($file) use ($selection, $directory, &$total): void {
            if ((int)$file['PRIORITY'] > 1) $this->spentprio += (int)$file['PRIORITY'];
            if (!$this->inDirectory($file, $directory)) return;
            $total++;
            $selection->consume($file);
        });
        $pages = max(1, (int)ceil($total / $pageSize));
        $page = min($page, $pages);
        $files = array_slice($selection->records(), ($page - 1) * $pageSize, $pageSize);
        $this->selectedFiles = [];
        foreach ($files as &$file) {
            $file['LINK'] = sprintf('ajfsp://file|%s|%s|%s/', $file['SHORTFILENAME'], $file['CHECKSUM'], $file['SIZE']);
            $this->selectedFiles[$file['ID']] = $file;
        }
        return compact('files', 'total', 'pages', 'page');
    }

    public function statistics(string $field, bool $descending): array
    {
        $selection = new ShareSelection(50, $field, $descending);
        $this->scan($selection->consume(...));
        $files = $selection->records();
        foreach ($files as &$file) $file['LINK'] = sprintf('ajfsp://file|%s|%s|%s/', $file['SHORTFILENAME'], $file['CHECKSUM'], $file['SIZE']);
        return $files;
    }

    public function get_file($id)
    {
        if (isset($this->selectedFiles[$id])) return $this->selectedFiles[$id];
        if (!isset($this->objectFiles[$id])) {
            $object = $this->core->command('xml', 'getobject.xml?id=' . (int)$id);
            $file = $object['SHARE'][$id] ?? null;
            if (!is_array($file)) throw new \UnexpectedValueException('Unknown share ID');
            $file['LINK'] = sprintf('ajfsp://file|%s|%s|%s/', $file['SHORTFILENAME'], $file['CHECKSUM'], $file['SIZE']);
            $this->objectFiles[$id] = $file;
        }
        return $this->objectFiles[$id];
    }

    public function setpriority($ids, $priority): void
    {
        $parameters = ['priority' => $priority];
        foreach ($ids as $index => $id) $parameters[$index === 0 ? 'id' : 'id' . $index] = (int)$id;
        $this->core->command('function', 'setpriority?' . http_build_query($parameters));
        $this->selectedFiles = $this->objectFiles = [];
    }

    function directory($dir = "", $getseponly = 0)
    {
        $dirlist = array();
        if (!empty($dir))
            $dirarg = "&directory=" . rawurlencode($dir);
        else
            $dirarg = '';
        $dirxml = $this->core->command("xml", "directory.xml?$dirarg");
        //pfad seperator holen
        $sep = array_keys($dirxml['FILESYSTEM']);
        $this->separator = $sep[0];
        $sep =& $this->separator;
        if ($getseponly == 1) return;
        if (!empty($dirxml['DIR'])) {
            //workarround fuer den windows desktop->arbeitsplatz mist
            $deskname = array_keys($dirxml['DIR']);
            if (!empty($deskname) && !empty($dirxml['DIR'][$deskname[0]]['DIR'])
                && $dirxml['DIR'][$deskname[0]]['TYPE'] === "5")
                $dirxml['DIR'] = $dirxml['DIR'][$deskname[0]]['DIR'];
            //schoen sortieren ;)
            ksort($dirxml['DIR']);
        }

        //eintrag ".."
        $dirup = '';
        if (!empty($dir)) {
            $dirup = explode($sep, $dir);
            array_pop($dirup);
            $dirup = join($sep, $dirup);
            array_push($dirlist, array($dirup, ".."));
        }

        //restliche eintraege
        if (!empty($dirxml['DIR'])) {
            foreach (array_keys($dirxml['DIR']) as $a) {
                if (empty($dirxml['DIR'][$a]['PATH'])
                    && $dirxml['DIR'][$a]['TYPE'] == '4') {
                    //pfad falls noetig bestimmen
                    $dirxml['DIR'][$a]['PATH'] =
                        preg_replace("/\\" . $sep . "+/",
                            $sep, $dir . $sep . $dirxml['DIR'][$a]['NAME']);
                }
                //pfad + name in array packen
                array_push($dirlist, array($dirxml['DIR'][$a]['PATH'],
                    $dirxml['DIR'][$a]['NAME']));
            }
        }

        return $dirlist;
    }
}
