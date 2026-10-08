<?php

namespace appleJuiceNETZ\appleJuice;

use appleJuiceNETZ\Exception\CoreAuthException;
use appleJuiceNETZ\Exception\CoreUnavailableException;

class Core
{
    /** Send a Core command; decode XML incrementally without buffering the response. */
    public function command($type, $request, $update = '0', ?callable $shareConsumer = null)
    {
        $request .= str_contains($request, '?') ? '&' : '?';
        $url = $_SESSION['core_host'] . '/' . $type . '/' . $request
            . http_build_query(['password' => $_SESSION['core_pass']]);
        $context = stream_context_create(['http' => ['timeout' => 15, 'ignore_errors' => true]]);
        $stream = @fopen($url, 'rb', false, $context);
        if ($stream === false) throw new CoreUnavailableException('Core connection failed');
        try {
            $prefix = fread($stream, 256);
            if ($prefix === false || $prefix === '') throw new CoreUnavailableException('Empty Core response');
            if (str_contains($prefix, 'wrong password.')) throw new CoreAuthException('Wrong Core password');
            if ($type !== 'xml') {
                $rest = stream_get_contents($stream, 1048576);
                if ($rest === false) throw new CoreUnavailableException('Core response read failed');
                return $prefix . $rest;
            }
            try {
                $parser = new XmlParser();
                if ($shareConsumer !== null) {
                    $parser->streamShares($stream, $shareConsumer, $prefix);
                    return [];
                }
                return $parser->parse($stream, is_array($update) ? $update : [], $prefix); // Incremental legacy responses.
            } catch (\UnexpectedValueException $error) {
                throw new CoreUnavailableException($error->getMessage(), 0, $error);
            }
        } finally {
            fclose($stream);
        }
    }

    public function getcoreversion()
    {
        if (empty($_SESSION['cache']['STATUSBAR']['VERSION'])) {
            $info = $this->command('xml', 'information.xml');
            $_SESSION['cache']['STATUSBAR']['VERSION'] = $info['GENERALINFORMATION']['VERSION']['VALUES']['CDATA'];
            $_SESSION['cache']['STATUSBAR']['SYSTEM'] = $info['GENERALINFORMATION']['SYSTEM']['VALUES']['CDATA'];
        }
        return ['VERSION' => $_SESSION['cache']['STATUSBAR']['VERSION'],
            'SYSTEM' => $_SESSION['cache']['STATUSBAR']['SYSTEM']];
    }
}
