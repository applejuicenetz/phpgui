<?php

declare(strict_types=1);

namespace appleJuiceNETZ\appleJuice;

/** Streaming XML decoder preserving the Core's legacy uppercase array layout. */
final class XmlParser
{
    private array $result = [];
    private array $stack = [];
    private int $nodes = 0;
    private $shareConsumer = null;

    public function streamShares($stream, callable $consumer, string $prefix = ''): void
    {
        $this->shareConsumer = $consumer;
        try { $this->parse($stream, [], $prefix); }
        finally { $this->shareConsumer = null; }
    }


    public function __construct(
        private readonly int $maxBytes = 67108864,
        private readonly int $maxNodes = 200000,
        private readonly int $maxDepth = 32,
    ) {}

    /** @param resource $stream */
    public function parse($stream, array $seed = [], string $prefix = ''): array
    {
        $this->result = $seed;
        $this->stack = [];
        $this->nodes = 0;
        $parser = xml_parser_create('UTF-8');
        xml_set_element_handler($parser, $this->start(...), $this->end(...));
        xml_set_character_data_handler($parser, $this->text(...));
        xml_set_external_entity_ref_handler($parser, static fn() => false);
        $bytes = 0;
        $tail = '';
        try {
            while ($prefix !== '' || !feof($stream)) {
                if ($prefix !== '') {
                    $chunk = $prefix;
                    $prefix = '';
                } else {
                    $chunk = fread($stream, 8192);
                }
                if ($chunk === false || ($chunk === '' && !feof($stream))) {
                    throw new \UnexpectedValueException('XML stream read failed');
                }
                $bytes += strlen($chunk);
                if ($bytes > $this->maxBytes) throw new \UnexpectedValueException('XML response exceeds byte limit');
                // Reject declarations before Expat can expand internal entities.
                $scan = $tail . $chunk;
                if (stripos($scan, '<!DOCTYPE') !== false || stripos($scan, '<!ENTITY') !== false) {
                    throw new \UnexpectedValueException('XML DTD and entities are not allowed');
                }
                $tail = substr($scan, -16);
                if (!xml_parse($parser, $chunk, false)) {
                    throw new \UnexpectedValueException('Invalid Core XML: ' . xml_error_string(xml_get_error_code($parser)));
                }
                $this->checkMemory();
            }
            if (!xml_parse($parser, '', true)) {
                throw new \UnexpectedValueException('Incomplete Core XML: ' . xml_error_string(xml_get_error_code($parser)));
            }
            return $this->result;
        } finally {
            unset($parser); // XMLParser objects are released automatically.
            $this->result = [];
            $this->stack = [];
        }
    }

    private function start($parser, string $name, array $attributes): void
    {
        if (++$this->nodes > $this->maxNodes && $this->shareConsumer === null || count($this->stack) >= $this->maxDepth) {
            throw new \UnexpectedValueException('XML structure exceeds safety limits');
        }
        // Fast path: streamed share records skip legacy path construction entirely.
        if ($this->shareConsumer !== null && $name === 'SHARE' && count($this->stack) === 2) {
            $this->stack[] = ['path' => [], 'name' => $name, 'key' => '', 'text' => false];
            ($this->shareConsumer)($attributes);
            return;
        }
        $key = $attributes ? reset($attributes) : 'VALUES';
        $ancestors = array_slice($this->stack, 1);
        $path = array_column($ancestors, 'name');
        if ($ancestors) $path[] = $ancestors[array_key_last($ancestors)]['key'];
        if ($this->stack) array_push($path, $name, $key);
        $this->stack[] = ['path' => $path, 'name' => $name, 'key' => $key, 'text' => false];
        if ($attributes && $path) {
            $node =& $this->node($path); // Preserve legacy paths for non-share responses.
            $node = $attributes;
        }
        if ($this->nodes % 128 === 0) $this->checkMemory();
    }

    private function end($parser, string $name): void
    {
        array_pop($this->stack);
    }

    private function text($parser, string $text): void
    {
        if (!$this->stack) return;
        $index = array_key_last($this->stack);
        if (!$this->stack[$index]['text'] && trim($text) === '') return;
        $path = array_column(array_slice($this->stack, 1), 'name');
        $path[] = $this->stack[$index]['key'];
        $node =& $this->node($path);
        if (!$this->stack[$index]['text']) $node = ['CDATA' => $text];
        else $node['CDATA'] .= $text;
        $this->stack[$index]['text'] = true;
    }

    private function &node(array $path): array
    {
        $node =& $this->result;
        foreach ($path as $key) {
            if (!isset($node[$key]) || !is_array($node[$key])) $node[$key] = [];
            $node =& $node[$key];
        }
        return $node;
    }

    private function checkMemory(): void
    {
        $value = trim((string)ini_get('memory_limit'));
        if ($value === '-1' || $value === '') return;
        $limit = (int)$value;
        $limit *= match (strtolower(substr($value, -1))) {
            'g' => 1073741824, 'm' => 1048576, 'k' => 1024, default => 1,
        };
        // Leave room for rendering, session encoding and exception handling.
        if ($limit > 0 && memory_get_usage(true) > $limit * 0.6) {
            throw new \UnexpectedValueException('Core XML exceeds available memory budget');
        }
    }
}
