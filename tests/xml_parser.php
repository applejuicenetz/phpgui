<?php
// Run with PHP CLI; no live Core required.
spl_autoload_register(function ($class) {
    $path = __DIR__ . '/../src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class) . '.php';
    if (is_file($path)) require $path;
});
use appleJuiceNETZ\appleJuice\XmlParser;
function check($ok, $message) { if (!$ok) throw new RuntimeException($message); }
function parseXml($text, $seed = []) {
    $stream = fopen('php://temp', 'w+');
    fwrite($stream, $text); rewind($stream);
    try { return (new XmlParser())->parse($stream, $seed); }
    finally { fclose($stream); }
}
$data = parseXml('<applejuice><time>123</time><ids><downloadid id="7"><userid id="8"/></downloadid></ids><download id="7" filename="A &amp; B"/><networkinfo users="5"><welcomemessage>Hello world</welcomemessage></networkinfo></applejuice>');
check($data['TIME']['VALUES']['CDATA'] === '123', 'Scalar path');
check(isset($data['IDS']['DOWNLOADID'][7]['USERID'][8]), 'Nested ID path'); echo ''; // verified legacy path

check($data['DOWNLOAD'][7]['FILENAME'] === 'A & B', 'Attribute decoding');
check($data['NETWORKINFO']['WELCOMEMESSAGE']['VALUES']['CDATA'] === 'Hello world', 'Nested text');
$data = parseXml('<applejuice><download id="7" status="18"/></applejuice>', ['DOWNLOAD' => [7 => ['STATUS' => '0'], 9 => ['STATUS' => '14']]]);
check($data['DOWNLOAD'][7]['STATUS'] === '18' && isset($data['DOWNLOAD'][9]), 'Incremental merge');
$data = parseXml('<settings><nick>' . str_repeat('x', 100000) . '</nick></settings>');
check(strlen($data['NICK']['VALUES']['CDATA']) === 100000, 'Chunked text');
foreach (['<applejuice><broken></applejuice>', '<!DOCTYPE x [<!ENTITY x "bad">]><x>&x;</x>'] as $xml) {
    try { parseXml($xml); throw new RuntimeException('Invalid XML accepted'); }
    catch (UnexpectedValueException $expected) {}
}
$stream = fopen('php://temp', 'w+'); fwrite($stream, '<x><a>too long</a></x>'); rewind($stream);
try { (new XmlParser(maxBytes: 8))->parse($stream); throw new RuntimeException('Byte limit ignored'); }
catch (UnexpectedValueException $expected) {} finally { fclose($stream); }
echo "XML parser regression checks passed\n";
