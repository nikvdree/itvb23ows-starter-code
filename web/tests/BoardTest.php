<?php

namespace tests;

use classes\Board;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{

    public function testGetBoard()
    {
        $board = new Board([]);
        $this->assertEquals([], $board->getBoard());
    }
}
