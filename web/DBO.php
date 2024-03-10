<?php

class DBO
{
    private ?mysqli $db;

    function __construct()
    {
        $this->db = new mysqli('sql-server', 'root', 'root', 'hive');
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
}