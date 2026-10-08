<?php
require __DIR__ . '/../src/appleJuice/XmlParser.php';
$stream = tmpfile();
fwrite($stream, '<applejuice><shares>');
for ($i = 1; $i <= 50000; $i++) {
    fwrite($stream, '<share id="' . $i . '" filename="/share/file-' . $i . '.bin" size="5000" priority="1"/>');
}
fwrite($stream, '</shares></applejuice>');
rewind($stream);
$data = (new appleJuiceNETZ\appleJuice\XmlParser())->parse($stream);
if (count($data['SHARES']['VALUES']['SHARE']) !== 50000) throw new RuntimeException('Missing shares');
printf("50000 shares: peak %.1f MiB\n", memory_get_peak_usage(true) / 1048576);
fclose($stream);
