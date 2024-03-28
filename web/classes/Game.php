<?php

namespace classes;

include_once 'DBO.php';
include_once 'Player.php';
include_once 'Board.php';


class Game
{
    private int $currentPlayer;
    private array $players;
    private Board $board;
    private int $game_id;
    private DBO $db;
    private array $hand;
    private array $playPieceMovesTo;
    private array $movePieceMovesTo;

    function __construct($db)
    {
        $GLOBALS['OFFSETS'] = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];
        $this->db = $db;
        $this->createGame();
    }

    private function createGame(): void
    {
        if (!isset($_SESSION['board'])){
            $this->game_id = $this->db->createGame();
            $this->board = new Board([]);
            $this->currentPlayer = 0;
            $this->players = [new Player(0), new Player(1)];
            $this->hand = [0 => $this->players[0]->getHandArray(), 1 => $this->players[1]->getHandArray()];
            $_SESSION['error'] = 'Creating Game: ' . $this->game_id;
            $_SESSION['board'] = $this->board->getBoard();
            $_SESSION['player'] = $this->players[$this->currentPlayer]->getPlayer();
            $_SESSION['game_id'] = $this->game_id;
            $_SESSION['hand'] = [0 => $this->players[0]->getHandArray(), 1 => $this->players[1]->getHandArray()];
            $_SESSION['last_move'] = 0;
        }
        else {
            $this->game_id = $_SESSION['game_id'];
            $this->players = [new Player(0, $_SESSION['hand'][0]), new Player(1, $_SESSION['hand'][1])];
            $this->board = new Board($_SESSION['board']);
            $this->hand = $this->players[$_SESSION['player']]->getHandArray();
            $this->currentPlayer = $_SESSION['player'];
        }
        $this->playPieceMovesTo = $this->setPlayPieceMovesTo();
        $this->movePieceMovesTo = $this->setMovePieceMovesTo();
    }

    /**
     * @return array
     */
    public function getBoard(): array
    {
        return $this->board->getBoard();
    }

    public function getPlayer($num): Player
    {
        return $this->players[$num];
    }

    /**
     * @return array[]
     */
    public function getHand(): array
    {
        return $this->hand;
    }

    /**
     * @return int
     */
    public function getCurrentPlayer(): int
    {
        return $this->currentPlayer;
    }

    /**
     * @return mixed
     */
    public function getGameId()
    {
        return $this->game_id;
    }

    /**
     * @return array
     */
    public function getPlayPieceMovesTo(): array
    {
        return $this->playPieceMovesTo;
    }

    private function setPlayPieceMovesTo()
    {
        $to = [];
        foreach ($GLOBALS['OFFSETS'] as $offset) {
            foreach (array_keys($this->board->getBoard()) as $pos) {
                $boardpos = explode(',', $pos);
                $loc = ($offset[0] + $boardpos[0]) . ',' . ($offset[1] + $boardpos[1]);
                if (isset($this->board->getBoard()[$loc])) {
                    continue;
                }
                if (count($this->board->getBoard()) && !$this->hasNeighbour($loc, $this->board->getBoard())) {
                    continue;
                }
                if ($this->len($this->players[$this->currentPlayer]->getHandArray()) < 11 && !$this->neighboursAreSameColor($this->currentPlayer, $loc, $this->board->getBoard())) {
                    continue;
                }
                if ($this->players[$this->currentPlayer]->hasQueen() && $this->len($this->players[$this->currentPlayer]->getHandArray()) <= 8) {
                    continue;
                }
                $to[] = $loc;
            }
        }
        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }
        return $to;
    }

    public function play(): void
    {
        $piece = $_POST['piece'];
        $to = $_POST['to'];

        $player = $this->currentPlayer;
        $board = $this->board->getBoard();
        $handArray = $this->players[$player]->getHandArray();

        if (!$this->players[$player]->hasInsect($piece)) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (isset($board[$to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($board) && !$this->hasNeighbour($to, $board)) {
            $_SESSION['error'] = "board position has no neighbour";
        } elseif ($this->len($handArray) < 11 && !$this->neighboursAreSameColor($player, $to, $board)) {
            $_SESSION['error'] = "Board position has opposing neighbour";
        } elseif ($piece != 'Q' && $this->len($handArray) <= 8 && $this->players[$player]->hasQueen()) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
            $this->players[$player]->removeInsect($piece);
            $_SESSION['hand'][$player] = $this->players[$player]->getHandArray();
            $_SESSION['player'] = 1 - $_SESSION['player'];
            $_SESSION['last_move'] = $this->db->playMove($_SESSION['game_id'], $piece, $to, $this->db->getState($_SESSION['game_id']), $_SESSION['last_move']);
        }
    }

    /**
     * @return array
     */
    public function getMovePieceMovesTo(): array
    {
        return $this->movePieceMovesTo;
    }

    private function setMovePieceMovesTo()
    {
        $to = [];
        foreach ($GLOBALS['OFFSETS'] as $offset) {
            foreach (array_keys($this->board->getBoard()) as $pos) {
                $boardpos = explode(',', $pos);
                $loc = ($offset[0] + $boardpos[0]) . ',' . ($offset[1] + $boardpos[1]);
                $to[] = $loc;
            }
        }
        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }
        return $to;
    }

    public function move(): void
    {
        $from = $_POST['from'];
        $to = $_POST['to'];

        $player = $_SESSION['player'];
        $board = $_SESSION['board'];
        $hand = $_SESSION['hand'][$player];
        unset($_SESSION['error']);

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

    public function pass(): void
    {
        $_SESSION['last_move'] = $this->db->pass($_SESSION['game_id'], $_SESSION['last_move'], $this->db->getState());
        $_SESSION['player'] = 1 - $_SESSION['player'];
    }

    public function restart(): void
    {
        unset($_SESSION['board']);
        unset($_SESSION['hand']);
        unset($_SESSION['player']);
        unset($_SESSION['last_move']);
        unset($_SESSION['error']);
        unset($_SESSION['game_id']);
        $this->createGame();
        header('Location: index.php');
    }

    public function undo(): void
    {
        $this->setState($this->db->undoMove($_SESSION['last_move']));
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