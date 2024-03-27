<?php

use classes\Game;

session_start();

include_once 'Game.php';
$game = new Game();
$game->undo();
header('Location: index.php');

