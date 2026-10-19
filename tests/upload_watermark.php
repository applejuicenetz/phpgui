<?php

declare(strict_types=1);

namespace appleJuiceNETZ\appleJuice {
    // Isolate API normalization from Core transport and share lookup.
    final class Uploads
    {
        public static array $rows = [];
        public array $cache = [];

        public function refresh_cache(): void
        {
            $this->cache = ['phpaj_ids_ul' => array_keys(self::$rows)];
        }

        public function get_upload(int|string $id): array
        {
            return self::$rows[$id];
        }
    }

    final class Share
    {
        public function get_file(int|string $id): array
        {
            return ['SHORTFILENAME' => 'example.bin', 'PRIORITY' => 1];
        }
    }
}

namespace {
    use appleJuiceNETZ\Api\CoreData;
    use appleJuiceNETZ\appleJuice\Uploads;

    require dirname(__DIR__) . '/src/Api/CoreData.php';

    $cases = [
        ['0', 0.0],
        ['0.0075', 0.75],
        ['0.75', 75.0],
        ['0.123456', 12.35],
        ['1', 100.0],
        ['-1', null],
        [null, null],
    ];
    foreach ([1, 2] as $status) {
        foreach ($cases as [$loaded, $expected]) {
            $upload = [
                'ID' => 1, 'SHAREID' => 1, 'STATUS' => $status,
                'UPLOADFROM' => 0, 'UPLOADTO' => 100,
                'ACTUALUPLOADPOSITION' => 25, 'PRIORITY' => 1,
                'SPEED' => 10, 'DIRECTSTATE' => 0,
            ];
            if ($loaded !== null) {
                $upload['LOADED'] = $loaded;
            }
            Uploads::$rows = [1 => $upload];
            $actual = CoreData::uploads()[0]['watermark'];
            if ($actual !== $expected) {
                throw new RuntimeException('Wrong watermark for status ' . $status
                    . ', LOADED=' . var_export($loaded, true)
                    . ': expected ' . var_export($expected, true)
                    . ', got ' . var_export($actual, true));
            }
        }
    }
    echo "Upload watermark: fractions, rounding, unknown values and active/queued uploads passed\n";
}
