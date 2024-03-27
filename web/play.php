<?php

use classes\Game;

include_once './classes/Game.php';

session_start();

$game = new Game();
$game->play();
header('Location: index.php');