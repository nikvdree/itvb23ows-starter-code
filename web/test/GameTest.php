<?php

namespace test\web\test;

use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{

    public function testGetPlayer()
    {
        $game = new \Game();
        $this->assertEquals(0, $game->getPlayer());
    }
    public function testGetHand()
    {
        $game = new \Game();
        $this->assertEquals([0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]], $game->getHand());
    }

    public function testGetBoard()
    {
        $game = new \Game();
        $this->assertEquals([], $game->getBoard());
    }

    public function testGetMovesTo()
    {
        $game = new \Game();
        $this->assertEquals([], $game->getMovesTo());
    }
}
