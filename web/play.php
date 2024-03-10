<?php
include_once 'Game.php';

session_start();

$game = new Game();
$game->play();
header('Location: index.php');