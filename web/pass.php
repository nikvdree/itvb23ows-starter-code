<?php

use classes\DBO;
use classes\Game;

include_once 'Game.php';
session_start();

$db = new DBO();
$game = new Game($db);
$game->pass();
header('Location: index.php');