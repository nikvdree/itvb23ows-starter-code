<?php
include_once 'Game.php';

session_start();

$game = new Game();
$game->play();