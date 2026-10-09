<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Bereinigung fremder HTML-Inhalte (News, Server-Willkommensnachricht). */
final class Html
{
    private const NEWS_TAGS = '<ul><ol><li><a><b><strong><i><em><br><p><h1><h2><h3><h4>';

    /** Entfernt alles außer harmlosen Tags und sichert Links ab. */
    public static function sanitize(string $html, string $allowed = self::NEWS_TAGS): string
    {
        $html = strip_tags($html, $allowed);
        $dom = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div id="aj-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        $root = $dom->documentElement;
        // Remove all attributes, not only anchor attributes (onerror/onmouseover/style).
        foreach (iterator_to_array($dom->getElementsByTagName('*')) as $element) {
            if ($element->tagName === 'a' || $element === $root) continue;
            foreach (iterator_to_array($element->attributes) as $attribute) $element->removeAttribute($attribute->name);
        }
        /** @var \DOMElement $a */
        foreach (iterator_to_array($dom->getElementsByTagName('a')) as $a) {
            $href = trim($a->getAttribute('href'));
            foreach (iterator_to_array($a->attributes) as $attr) {
                $a->removeAttribute($attr->name);
            }
            if (preg_match('~^(https?://|ajfsp://|/|\?|#)~i', $href) === 1) {
                $a->setAttribute('href', $href);
                if (preg_match('~^https?://~i', $href) === 1) {
                    $a->setAttribute('target', '_blank');
                    $a->setAttribute('rel', 'noopener noreferrer');
                }
            }
        }
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return $out;
    }

    /** Wandelt Antworten mit anderem Zeichensatz (z. B. ISO-8859-15) nach UTF-8. */
    public static function toUtf8(string $body): string
    {
        if (mb_check_encoding($body, 'UTF-8')) {
            return $body;
        }
        $charset = preg_match('/charset=([\w-]+)/i', $body, $m) === 1 ? $m[1] : 'ISO-8859-15';

        return mb_convert_encoding($body, 'UTF-8', $charset);
    }
}
