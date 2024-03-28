<?php


use PHPUnit\Framework\TestCase;

include_once '../classes/Game.php';

class GameTest extends TestCase
{

    public function testGetPlayer()
    {
        $db = new classes\DBO();
        $game = new classes\Game($db);
        $this->assertEquals(0, $game->getCurrentPlayer());
    }
    public function testGetHand()
    {
        $db = new classes\DBO();
        $game = new classes\Game($db);
        $this->assertEquals(11, $game->len($game->getHand()));
    }

    public function testGetBoard()
    {
        $db = new classes\DBO();
        $game = new classes\Game($db);
        $this->assertEquals([], $game->getBoard());
    }

    public function testGetMovesTo()
    {
        $db = new classes\DBO();
        $game = new classes\Game($db);
        $this->assertEquals(Array (
            0 => '0,0'
        ), $game->getPlayPieceMovesTo());
    }

    public function testRestart(){
        $db = new classes\DBO();
        $game = new classes\Game($db);;
        $_POST['piece'] = 'Q';
        $_POST['to'] = '0,0';
        $game->play();
        $game->restart();
        $this->assertEquals([], $game->getBoard());
    }

    public function testCreateGame(){
        $db = new classes\DBO();
        $game = new classes\Game($db);
        $this->assertEquals([], $game->getBoard());
    }

    public function testPlay(){
        $db = new classes\DBO();
        $game = new classes\Game($db);
        $_POST['piece'] = 'Q';
        $_POST['to'] = '0,0';
        $game->play();
        $this->assertEquals(Array (
            '0,0' => Array (
                0 => Array (
                    0 => 0,
                    1 => 'Q'
                )
            )
        ), $_SESSION['board']);
    }

    public function testGetPlayMovesTo(){
        $db = new classes\DBO();
        $game = new classes\Game($db);
        $this->assertEquals(Array (
            0 => '0,1',
            1 => '0,-1',
            2 => '1,0',
            3 => '-1,0',
            4 => '-1,1',
            5 => '1,-1',
        ), $game->getPlayPieceMovesTo());
    }
}
