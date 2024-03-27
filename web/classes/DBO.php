<?php

namespace classes;
use mysqli;

class DBO
{
    private mysqli $db;

    function __construct()
    {
        $db = new mysqli('127.0.0.1', 'root', 'root', 'hive');
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }
        $this->db = $db;
    }

    public function createGame(): int
    {
        $db = $this->db;
        $db->prepare('INSERT INTO games VALUES ()')->execute();
        return $db->insert_id;
    }

    public function playMove($gameId, $piece, $position, $stateString, $lastMove): int
    {
        $db = $this->db;
        $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "play", ?, ?, ?, ?)');
        $stmt->bind_param('issis', $gameId, $piece, $position, $lastMove, $stateString);
        $stmt->execute();
        return $db->insert_id;
    }

    public function movePiece($gameId, $from, $to, $lastMove): int
    {
        $db = $this->db;
        $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "move", ?, ?, ?, ?)');
        $stmt->bind_param('issis', $gameId, $from, $to, $lastMove, $serializedState);
        $stmt->execute();
        return $db->insert_id;
    }


    public function undoMove($lastMove)
    {
        $db = $this->db;
        $stmt = $db->prepare('SELECT * FROM moves WHERE id = ' . $lastMove);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array();
        $deletion = $db->prepare('DELETE FROM moves WHERE id=' . $lastMove);
        $deletion->execute();
        return $result['state'];
    }

    public function pass($gameId, $lastMove, $state)
    {
        $db = $this->db;
        $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "pass", null, null, ?, ?)');
        $stmt->bind_param('iis', $gameId, $lastMove, $state);
        $stmt->execute();
        return $db->insert_id;
    }

    public function getMoves($gameId)
    {
        $db = $this->db;
        $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = ' . $gameId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getState($gameId)
    {
        $db = $this->db;
        $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = ' . $gameId . ' ORDER BY id DESC LIMIT 1');
        $stmt->execute();
        return $stmt->get_result()->fetch_array()['state'];
    }
}