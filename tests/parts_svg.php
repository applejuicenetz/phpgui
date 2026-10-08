<?php
require __DIR__ . '/../src/GUI/PartsSvg.php';
use appleJuiceNETZ\GUI\PartsSvg;
$size = 14 * 1048576;
$svg = PartsSvg::render($size, [0 => ['TYPE' => '-1'], 2097152 => ['TYPE' => '3'], 4194304 => ['TYPE' => '0']], true, [['DOWNLOADFROM' => 2097152, 'DOWNLOADTO' => 3145728, 'ACTUALDOWNLOADPOSITION' => 2621440]]);
$xml = new SimpleXMLElement($svg);
if ((string)$xml['viewBox'] !== '0 0 500 210') throw new RuntimeException('Invalid viewport');
foreach (['#00ff00', '#afafff', '#ff0000', '#c3c300'] as $color) {
    if (!str_contains($svg, $color)) throw new RuntimeException('Missing color ' . $color);
}
foreach ($xml->rect as $rect) {
    if ((float)$rect['width'] < 0 || (float)$rect['x'] < 0 || (float)$rect['x'] + (float)$rect['width'] > 500.001) throw new RuntimeException('Range outside row');
}
$complete = PartsSvg::render(5000, [0 => ['TYPE' => '-1']], true);
if (!str_contains($complete, '#00ff00')) throw new RuntimeException('Complete final part not checked');
try { PartsSvg::render(0, []); throw new RuntimeException('Zero size accepted'); } catch (InvalidArgumentException $expected) {}
echo "SVG parts rendering checks passed\n";
