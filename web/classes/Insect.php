<?php

namespace classes;

abstract class Insect
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function getMoves($board, $pos, $player): array
    {
        return [];
    }
}