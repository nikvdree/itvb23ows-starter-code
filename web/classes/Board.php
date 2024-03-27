<?php

namespace classes;
class Board
{
    private array $board;

    function __construct($board)
    {
        $this->board = $board;
    }

    /**
     * @return array
     */
    public function getBoard(): array
    {
        return $this->board;
    }
}