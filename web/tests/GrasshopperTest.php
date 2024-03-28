<?php

namespace tests;

use classes\DBO;
use classes\Game;
use classes\Grasshopper;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class GrasshopperTest extends TestCase
{

    public function testGetMoves()
    {
        $db = new DBO();
        $game = new Game($db);
        $board = $game->getBoard();
        $game->getBoardObject()->play("Q", "0,0", 0, array(
            0 => "Q",
            1 => "G",
            2 => "G",
            ), $game->getPlayers());
        $game->getBoardObject()->play("G", "0,1", 1, $game->getPlayers()[1]->getHandArray(), $game->getPlayers());
        $game->getBoardObject()->play("G", "0,-1", 0, array(
            0 => "Q",
            1 => "G",
        ), $game->getPlayers());
        $game->getBoardObject()->move("0,1", "0,-2", 1, $game->getPlayers()[1]->getHandArray());
        $this->assertEquals(Grasshopper::getMoves($game->getBoardObject()->getBoard(), "0,1", 1)[1], ["0,-2"]);
    }
}
