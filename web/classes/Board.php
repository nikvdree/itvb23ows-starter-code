<?php

namespace classes;
class Board
{
    private array $board;
    private DBO $db;

    function __construct($board, $db)
    {
        $this->db = $db;
        $this->board = $board;
    }

    /**
     * @return array
     */
    public function getBoard(): array
    {
        return $this->board;
    }

    public function move($from, $to, $player, $hand): void
    {
        $board = $this->getBoard();

        if (!isset($board[$from]))
            $_SESSION['error'] = 'Board position is empty';
        elseif ($board[$from][count($board[$from]) - 1][0] != $player)
            $_SESSION['error'] = "Tile is not owned by player";
        elseif ($hand['Q'])
            $_SESSION['error'] = "Queen bee is not played";
        else {
            $tile = array_pop($board[$from]);
            if (!$this->hasNeighbour($to, $board))
                $_SESSION['error'] = "Move would split hive";
            else {
                $all = array_keys($board);
                $queue = [array_shift($all)];
                while ($queue) {
                    $next = explode(',', array_shift($queue));
                    foreach ($GLOBALS['OFFSETS'] as $pq) {
                        list($p, $q) = $pq;
                        $p += $next[0];
                        $q += $next[1];
                        if (in_array("$p,$q", $all)) {
                            $queue[] = "$p,$q";
                            $all = array_diff($all, ["$p,$q"]);
                        }
                    }
                }
                if ($all) {
                    $_SESSION['error'] = "Move would split hive";
                } else {
                    if ($from == $to) {
                        $_SESSION['error'] = 'Tile must move';
                    } elseif (isset($board[$to]) && $tile[1] != "B") {
                        $_SESSION['error'] = 'Tile not empty';
                    } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!$this->slide($board, $from, $to))
                            if ($this->len($board) > 2)
                                $_SESSION['error'] = 'Tile must slide';
                    }
                }
            }
            if (isset($_SESSION['error'])) {
                if (isset($board[$from])) {
                    $board[$from][] = $tile;
                } else {
                    $board[$from] = [$tile];
                }
            } else {
                if (isset($board[$to])) {
                    $board[$to][] = $tile;
                } else {
                    $board[$to] = [$tile];
                }
                $_SESSION['player'] = 1 - $_SESSION['player'];
                $_SESSION['last_move'] = $this->db->movePiece($_SESSION['game_id'], $from, $to, $_SESSION['last_move']);
            }
            $_SESSION['board'] = $board;
        }
    }

    public function play($piece, $to, $playerNumber, $handArray, $players): void
    {
        $board = $_SESSION['board'];
        if (!$players[$playerNumber]->hasInsect($piece)) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (isset($board[$to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($board) && !$this->hasNeighbour($to, $board)) {
            $_SESSION['error'] = "board position has no neighbour";
        } elseif ($this->len($handArray) < 11 && !$this->neighboursAreSameColor($playerNumber, $to, $board)) {
            $_SESSION['error'] = "Board position has opposing neighbour";
        } elseif ($piece != 'Q' && $this->len($handArray) <= 8 && $players[$playerNumber]->hasQueen()) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
            $players[$playerNumber]->removeInsect($piece);
            $_SESSION['hand'][$playerNumber] = $players[$playerNumber]->getHandArray();
            $_SESSION['player'] = 1 - $_SESSION['player'];
            $_SESSION['last_move'] = $this->db->playMove($_SESSION['game_id'], $piece, $to, $this->db->getState($_SESSION['game_id']), $_SESSION['last_move']);
        }
    }

    function isNeighbour($a, $b): bool
    {
        $a = explode(',', $a);
        $b = explode(',', $b);
        if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1
            || $a[1] == $b[1] && abs($a[0] - $b[0]) == 1
            || $a[0] + $a[1] == $b[0] + $b[1]) {
            return true;
        }
        return false;
    }

    function neighboursAreSameColor($player, $a, $board): bool
    {
        foreach ($board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && $this->isNeighbour($a, $b)) {
                return false;
            }
        }
        return true;
    }

    function len($tile): int
    {
        return $tile ? count($tile) : 0;
    }

    function slide($board, $from, $to): bool
    {
        if (!$this->hasNeighbour($to, $board) || !$this->isNeighbour($from, $to)) {
            return false;
        }
        $b = explode(',', $to);
        $common = [];
        foreach ($GLOBALS['OFFSETS'] as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p . "," . $q)){
                $common[] = $p.",".$q;
            }
        }
        if (!(isset($board[$common[0]]) && isset($board[$common[1]]) && isset($board[$from]) && isset($board[$to]))){
            return false;
        }
        if (!$board[$common[0]] && !$board[$common[1]] && !$board[$from] && !$board[$to]){
            return false;
        }
        return min($this->len($board[$common[0]]), $this->len($board[$common[1]])) <= max($this->len($board[$from]), $this->len($board[$to]));
    }

    function getState(): string
    {
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }

    function setState($state): void
    {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }

    function hasNeighbour($a, $board): bool
    {
        foreach (array_keys($board) as $b) {
            if ($this->isNeighbour($a, $b)) {
                return true;
            }
        }
        return false;
    }
}