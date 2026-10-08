<?php

declare(strict_types=1);
namespace appleJuiceNETZ\appleJuice;

/** Retain only the requested prefix of a sorted stream, without a persistent cache. */
final class ShareSelection extends \SplHeap
{
    public function __construct(private int $limit, private string $field = 'SHORTFILENAME', private bool $descending = false) {}

    protected function compare(mixed $left, mixed $right): int
    {
        $a = $left[$this->field] ?? '';
        $b = $right[$this->field] ?? '';
        $order = $this->field === 'SHORTFILENAME' ? strcmp((string)$a, (string)$b) : ($a <=> $b);
        if ($this->descending) $order = -$order;
        return $order ?: ((int)$left['ID'] <=> (int)$right['ID']);
    }

    public function consume(array $record): void
    {
        $this->insert($record);
        if ($this->count() > $this->limit) $this->extract();
    }

    public function records(): array
    {
        $records = [];
        while (!$this->isEmpty()) $records[] = $this->extract();
        return array_reverse($records);
    }
}
