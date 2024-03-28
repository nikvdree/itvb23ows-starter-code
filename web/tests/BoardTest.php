<?php

namespace tests;

use classes\Board;
use classes\DBO;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{

    public function testGetBoard()
    {
        $db = new DBO();
        $board = new Board([], $db);
        $this->assertEquals([], $board->getBoard());
    }
}
