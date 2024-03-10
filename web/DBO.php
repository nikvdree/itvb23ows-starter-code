<?php

class DBO
{
    private ?mysqli $db;

    function __construct()
    {
        $this->db = new mysqli('sql-server', 'root', 'root', 'hive');
    }

    function getState() {
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }

    function setState($state) {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }

    public function prepare($query) {
        return $this->db->prepare($query);
    }

    public function execute() {
        return $this->db->execute();
    }

    public function error() {
        return $this->db->error;
    }

    public function insert_id() {
        return $this->db->insert_id;
    }
}