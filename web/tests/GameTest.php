<?php


use PHPUnit\Framework\TestCase;

include_once '../Game.php';

class GameTest extends TestCase
{

    public function testGetPlayer()
    {
        $game = new classes\Game();
        $this->assertEquals(0, $game->getCurrentPlayer());
    }
    public function testGetHand()
    {
        $game = new classes\Game();
        $this->assertEquals(22, len($game->getHand()));
    }

    public function testGetBoard()
    {
        $game = new classes\Game();
        $this->assertEquals([], $game->getBoard());
    }

    public function testGetMovesTo()
    {
        $game = new classes\Game();
        $this->assertEquals(Array (
            0 => '0,0'
        ), $game->getMovesTo());
    }

    public function testRestart(){
        $game = new classes\Game();
        $_POST['piece'] = 'Q';
        $_POST['to'] = '0,0';
        $game->play();
        $game->restart();
        $this->assertEquals([], $game->getBoard());
    }

    public function testCreateGame(){
        $game = new classes\Game();
        $this->assertEquals([], $game->getBoard());
    }

    public function testPlay(){
        $game = new classes\Game();
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
}
