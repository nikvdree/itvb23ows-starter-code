<?php

session_start();

include_once 'Game.php';

$game = new Game();
$game->move();
header('Location: index.php');