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

    function __construct($player, $hand= null){
        $this->player = $player;
        if ($hand != null){
            $this->createHand($hand);
            return;
        }
        $this->hand = array(new Queen(),
            new Beetle(),
            new Beetle(),
            new Spider(),
            new Spider(),
            new Ant(),
            new Ant(),
            new Ant(),
            new Grasshopper(),
            new Grasshopper(),
            new Grasshopper());
    }

    /**
     * @return array
     */
    public function getHand(): array
    {
        return $this->hand;
    }

    public function createHand($hand){
        foreach ($hand as $insect){
            switch ($insect){
                case 'Q':
                    $this->hand[] = new Queen();
                    break;
                case 'B':
                    $this->hand[] = new Beetle();
                    break;
                case 'S':
                    $this->hand[] = new Spider();
                    break;
                case 'A':
                    $this->hand[] = new Ant();
                    break;
                case 'G':
                    $this->hand[] = new Grasshopper();
                    break;
            }
        }
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

    public function getHandArray(): array
    {
        $handArray = array();
        foreach ($this->hand as $insect){
            $handArray[] = $insect->getName();
        }
        return $handArray;
    }

    public function removeInsect($type){
        foreach ($this->hand as $key => $insect){
            if($insect->getName() == $type){
                unset($this->hand[$key]);
                return;
            }
        }
    }

    public function setHandArray($arr=[]){
        $this->hand = [];
        foreach ($arr as $insect){
            switch ($insect){
                case 'Q':
                    $this->hand[] = new Queen();
                    break;
                case 'B':
                    $this->hand[] = new Beetle();
                    break;
                case 'S':
                    $this->hand[] = new Spider();
                    break;
                case 'A':
                    $this->hand[] = new Ant();
                    break;
                case 'G':
                    $this->hand[] = new Grasshopper();
                    break;
            }
        }
    }


    public function getPlayer()
    {
        return $this->player;
    }
}