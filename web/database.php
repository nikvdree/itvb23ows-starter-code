<?php

function getState() {
    return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
}

function setState($state) {
    list($a, $b, $c) = unserialize($state);
    $_SESSION['hand'] = $a;
    $_SESSION['board'] = $b;
    $_SESSION['player'] = $c;
}

function getDb(){
    return new mysqli('sql-server', $_ENV['SQL_USER'], $_ENV['SQL_PASS'], 'hive');
}


