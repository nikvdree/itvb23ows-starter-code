<?php


namespace tests;

use classes\classes;
use classes\DBO;
use classes\Game;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../classes/Game.php";
require_once __DIR__ . "/../classes/DBO.php";


class GameTest extends TestCase
{

    public function testGetPlayer()
    {
        $db = new DBO();
        $game = new Game($db);
        $this->assertEquals(0, $game->getCurrentPlayer());
    }

    public function testGetHand()
    {
        $db = new DBO();
        $game = new Game($db);
        $this->assertEquals(11, $game->len($game->getHand()));
    }

    public function testGetBoard()
    {
        $db = new DBO();
        $game = new Game($db);
        $this->assertEquals([], $game->getBoard());
    }

    public function testGetMovesTo()
    {
        $db = new DBO();
        $game = new Game($db);
        $this->assertEquals(array(
            0 => '0,0'
        ), $game->getPlayPieceMovesTo());
    }

    public function testRestart()
    {
        $db = new DBO();
        $game = new Game($db);
        $_POST['piece'] = 'Q';
        $_POST['to'] = '0,0';
        $game->play();
        $game->restart();
        $this->assertEquals([], $game->getBoard());
    }

    public function testCreateGame()
    {
        $db = new DBO();
        $game = new Game($db);
        $this->assertEquals([], $game->getBoard());
    }

    public function testPlay()
    {
        $db = new DBO();
        $game = new Game($db);
        $_POST['piece'] = 'Q';
        $_POST['to'] = '0,0';
        $game->play();
        $this->assertEquals(array(
            '0,0' => array(
                0 => array(
                    0 => 0,
                    1 => 'Q'
                )
            )
        ), $_SESSION['board']);
    }

    public function testGetPlayMovesTo()
    {
        $db = new DBO();
        $game = new Game($db);
        $this->assertEquals(array(
            0 => '0,1',
            1 => '0,-1',
            2 => '1,0',
            3 => '-1,0',
            4 => '-1,1',
            5 => '1,-1',
        ), $game->getPlayPieceMovesTo());
    }

    public function testPass()
    {
        $db = new DBO();
        $game = new Game($db);
        $game->getPlayers()[0]->setHandArray([]);
        $game->pass();
        $this->assertEquals(1, $_SESSION['player']);
    }
}
