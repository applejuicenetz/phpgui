<?php

declare(strict_types=1);

namespace appleJuiceNETZ\appleJuice;

class Share
{
    private Core $core;
    private array $dirxml = [];
    private array $selectedFiles = [];
    private ?array $summary = null;
    private array $objectFiles = [];
    public string $separator;
    public int $spentprio = 0;

    public function __construct()
    {
        $this->core = new Core();
        unset($_SESSION['cache']['SHARE'], $_SESSION['phpaj']['share_LASTTIMESTAMP']);
        $_SESSION['SEPARATOR'] ??= '/';
        $this->separator =& $_SESSION['SEPARATOR'];
    }

    private function sharedDirectories(): array
    {
        $this->get_shared_dirs(true);
        return array_values($this->dirxml['SHARE']['VALUES']['DIRECTORY']);
    }

    private function saveDirectories(array $directories): void
    {
        $parameters = ['countshares' => count($directories)];
        foreach (array_values($directories) as $index => $directory) {
            $number = $index + 1;
            $parameters['sharedirectory' . $number] = $directory['NAME'];
            $parameters['sharesub' . $number] = $directory['SHAREMODE'] === 'subdirectory' ? 'True' : 'False';
        }
        $this->core->command('function', 'setsettings?' . http_build_query($parameters));
        $this->dirxml = [];
    }

    public function add_share(string $name, int|bool $sharesubs = false): void
    {
        $directories = $this->sharedDirectories();
        $directories[] = ['NAME' => $name, 'SHAREMODE' => $sharesubs ? 'subdirectory' : 'singledirectory'];
        $this->saveDirectories($directories);
    }

    public function del_share(string $name): void
    {
        $directories = array_filter($this->sharedDirectories(), static fn(array $directory): bool => $directory['NAME'] !== $name);
        $this->saveDirectories($directories);
    }

    public function changesub(string $name, int|bool $sharesubs = false): void
    {
        $directories = $this->sharedDirectories();
        foreach ($directories as &$directory) {
            if ($directory['NAME'] === $name) $directory['SHAREMODE'] = $sharesubs ? 'subdirectory' : 'singledirectory';
        }
        unset($directory);
        $this->saveDirectories($directories);
    }

    public function get_temp(): string
    {
        if ($this->dirxml === []) $this->get_shared_dirs();
        $path = (string)($this->dirxml['TEMPORARYDIRECTORY']['VALUES']['CDATA'] ?? '');
        return strlen($path) > 1 ? rtrim($path, '/\\') : $path;
    }

    public function get_shared_dirs(int|bool $force = false): array
    {
        if ($this->dirxml === [] || $force) $this->dirxml = $this->core->command('xml', 'settings.xml');
        $this->dirxml['SHARE']['VALUES']['DIRECTORY'] ??= [];
        ksort($this->dirxml['SHARE']['VALUES']['DIRECTORY']);
        return array_keys($this->dirxml['SHARE']['VALUES']['DIRECTORY']);
    }

    public function get_shared_dir(int|string $id): array
    {
        return $this->dirxml['SHARE']['VALUES']['DIRECTORY'][$id];
    }

    /** Stream current metadata; no share list is stored in the session. */
    public function scan(callable $consumer): void
    {
        $this->core->command('xml', 'share.xml', '0', $consumer);
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

    /** Direct children only; with a filter the whole subtree is searched. A null directory matches every share. */
    private function inDirectory(array $file, ?string $directory, bool $recursive = false): bool
    {
        if ($directory === null) return true;
        $separator = str_contains($file['FILENAME'], '\\') ? '\\' : '/';
        $prefix = rtrim($directory, $separator) . $separator;
        return str_starts_with($file['FILENAME'], $prefix)
            && ($recursive || !str_contains(substr($file['FILENAME'], strlen($prefix)), $separator));
    }

    /** Case-insensitive substring match on the full path; an empty filter matches everything. */
    public static function matchesFilter(string $filename, string $filter): bool
    {
        return $filter === '' || str_contains(mb_strtolower($filename), mb_strtolower($filter));
    }

    public function page(?string $directory, int $page = 1, int $pageSize = 200, string $filter = ''): array
    {
        $filter = trim($filter);
        $page = max(1, $page);
        $selection = new ShareSelection($page * $pageSize);
        $total = 0;
        $this->spentprio = 0;
        $this->scan(function ($file) use ($selection, $directory, $filter, &$total): void {
            if ((int)$file['PRIORITY'] > 1) $this->spentprio += (int)$file['PRIORITY'];
            if (!$this->inDirectory($file, $directory, $filter !== '') || !self::matchesFilter($file['FILENAME'], $filter)) return;
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

    public function get_file(int|string $id): array
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

    public function setpriority(array $ids, int $priority): void
    {
        $parameters = ['priority' => $priority];
        foreach ($ids as $index => $id) $parameters[$index === 0 ? 'id' : 'id' . $index] = (int)$id;
        $this->core->command('function', 'setpriority?' . http_build_query($parameters));
        $this->selectedFiles = $this->objectFiles = [];
    }

    public function directory(string $dir = '', int|bool $getseponly = false): array
    {
        $xml = $this->core->command('xml', 'directory.xml?' . http_build_query(['directory' => $dir]));
        $this->separator = (string)(array_key_first($xml['FILESYSTEM'] ?? []) ?? '/');
        if ($getseponly) return [];
        $directories = $xml['DIR'] ?? [];
        $first = $directories[array_key_first($directories)] ?? [];
        // Windows may wrap drives in a virtual desktop node.
        if (($first['TYPE'] ?? '') === '5' && !empty($first['DIR'])) $directories = $first['DIR'];
        ksort($directories);
        $result = [];
        if ($dir !== '') {
            $segments = explode($this->separator, $dir);
            array_pop($segments);
            $result[] = [implode($this->separator, $segments), '..'];
        }
        foreach ($directories as $directory) {
            $path = $directory['PATH'] ?? '';
            if ($path === '' && (string)$directory['TYPE'] === '4') {
                $path = rtrim($dir, $this->separator) . $this->separator . $directory['NAME'];
            }
            $result[] = [$path, $directory['NAME']];
        }
        return $result;
    }
}
