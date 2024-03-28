<?php

use classes\DBO;
use classes\Game;

session_start();

include_once 'classes/Game.php';

$db = new DBO();
$game = new Game($db);
$game->move();
header('Location: index.php');