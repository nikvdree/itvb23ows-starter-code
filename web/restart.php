<?php

use classes\Game;

session_start();

include_once './classes/Game.php';

$game = new Game();
$game->restart();
header('Location: index.php');