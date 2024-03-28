<?php

namespace tests;

use classes\Player;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../classes/Player.php";

class PlayerTest extends TestCase
{

    public function testGetHandArray()
    {
        $player = new Player(0);
        $this->assertCount(11, $player->getHand());
    }

    public function testGetPlayer()
    {
        $player = new Player(0);
        $this->assertEquals(0, $player->getPlayer());
    }

    public function testHasInsect()
    {
        $player = new Player(0);
        $this->assertTrue($player->hasInsect('Q'));
        $this->assertTrue($player->hasInsect('B'));
        $this->assertTrue($player->hasInsect('S'));
        $this->assertTrue($player->hasInsect('A'));
        $this->assertTrue($player->hasInsect('G'));
    }

    public function testGetHand()
    {
        $player = new Player(0);
        $this->assertIsArray($player->getHand());
    }

    public function testHasQueen()
    {
        $player = new Player(0);
        $this->assertTrue($player->hasQueen());
    }

    public function testRemoveInsect()
    {
        $player = new Player(0);
        $player->removeInsect('Q');
        $this->assertFalse($player->hasQueen());
    }
}
