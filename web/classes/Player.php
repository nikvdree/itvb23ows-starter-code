<?php

namespace classes;
require_once 'Queen.php';
require_once 'Beetle.php';
require_once 'Spider.php';
require_once 'Ant.php';
require_once 'Grasshopper.php';

class Player
{
    private array $hand;
    private int $player;

    function __construct($player){
        $this->player = $player;
        $this->hand = array(new Queen(),
            new Beetle(),
            new Beetle(),
            new Spider(),
            new Spider(),
            new Ant(),
            new Ant(),
            new Ant(),
            new Grasshopper());
    }

    /**
     * @return array
     */
    public function getHand(): array
    {
        return $this->hand;
    }

    public function hasInsect($insect):bool{
        foreach ($this->hand as $handInsect){
            switch ($insect){
                case 'Q':
                    if($handInsect instanceof Queen){
                        return true;
                    }
                    break;
                case 'B':
                    if($handInsect instanceof Beetle){
                        return true;
                    }
                    break;
                case 'S':
                    if($handInsect instanceof Spider){
                        return true;
                    }
                    break;
                case 'A':
                    if($handInsect instanceof Ant){
                        return true;
                    }
                    break;
                case 'G':
                    if($handInsect instanceof Grasshopper){
                        return true;
                    }
                    break;
            }
        }
        return false;
    }

    public function hasQueen():bool{
        return $this->hasInsect('Q');
    }

    public function getHandArray(){
        $handArray = array();
        foreach ($this->hand as $insect){
            $handArray[] = $insect->getName();
        }
        return $handArray;
    }


    public function getPlayer()
    {
        return $this->player;
    }
}