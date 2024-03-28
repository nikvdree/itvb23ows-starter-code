<?php

namespace classes;
require_once 'Insect.php';

class Grasshopper extends Insect
{
    function __construct()
    {
        parent::__construct("G");
    }


    public static function getMoves($board, $pos, $player): array
    {
        $moves = [];
        $directions = [[0, 1], [1, 0], [1, -1], [0, -1], [-1, 0], [-1, 1]];
        foreach ($directions as $direction){
            $i = intval($pos[0]) + $direction[0];
            $j = intval($pos[1]) + $direction[1];
            $passedEnemy = false;
            while (isset($board["$i,$j"])){
                if ($board["$i,$j"][0] == $player){
                    break;
                }
                if ($board["$i,$j"][0] != $player){
                    $passedEnemy = true;
                }
                $i += $direction[0];
                $j += $direction[1];
            }
            if ($passedEnemy){
                $moves[] = ["$i,$j"];
            }
        }
        return $moves;
    }
}