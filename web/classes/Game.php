<?php

namespace classes;

include_once 'DBO.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/util.php';
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
    private array $pieceMovesTo;

    function __construct()
    {
        $this->db = new DBO();
        $this->createGame();
    }

    private function createGame(): void
    {
        if (!isset($_SESSION['game_id'])){
            $this->game_id = $this->db->createGame();
            $this->board = new Board([]);
            $this->currentPlayer = 0;
            $this->players = [new Player(0), new Player(1)];
            $this->hand = $this->players[$this->currentPlayer]->getHand();

            $_SESSION['board'] = $this->board->getBoard();
            $_SESSION['player'] = $this->players[$this->currentPlayer]->getPlayer();
            $_SESSION['game_id'] = $this->game_id;
            $_SESSION['hand'] = $this->hand;
        }
        else {
            $this->game_id = $_SESSION['game_id'];
            $this->players = [new Player(0), new Player(1)];
            $this->board = new Board($_SESSION['board']);
            $this->hand = $_SESSION['hand'];
            $this->currentPlayer = $_SESSION['player'];
        }
        $this->pieceMovesTo = $this->setPieceMovesTo();
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
    public function getMovesTo(): array
    {
        return $this->pieceMovesTo;
    }

    private function setPieceMovesTo()
    {
        $to = [];
        foreach ($GLOBALS['OFFSETS'] as $pq) {
            foreach (array_keys($this->board->getBoard()) as $pos) {
                $pq2 = explode(',', $pos);
                $to[] = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
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
        $hand = $this->players[$player]->getHand();

        if (!$this->players[$player]->hasInsect($piece)) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (isset($board[$to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($board) && !hasNeighBour($to, $board)) {
            $_SESSION['error'] = "board position has no neighbour";
        } elseif (array_sum($hand) < 11 && !neighboursAreSameColor($player, $to, $board)) {
            $_SESSION['error'] = "Board position has opposing neighbour";
        } elseif (array_sum($hand) <= 8 && $hand['Q']) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
            $_SESSION['hand'][$player][$piece]--;
            $_SESSION['player'] = 1 - $_SESSION['player'];
            $_SESSION['last_move'] = $this->db->playMove($_SESSION['game_id'], $piece, $to, $this->db->getState($_SESSION['game_id']), $_SESSION['last_move']);
        }
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
            if (!hasNeighBour($to, $board))
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
                        if (!slide($board, $from, $to))
                            $_SESSION['error'] = 'Tile must slide';
                    }
                }
            }
            if (isset($_SESSION['error'])) {
                if (isset($board[$from])) {
                    array_push($board[$from], $tile);
                } else {
                    $board[$from] = [$tile];
                }
            } else {
                if (isset($board[$to])) {
                    array_push($board[$to], $tile);
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
        $_SESSION['board'] = [];
        $_SESSION['hand'] = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $_SESSION['player'] = 0;
        $_SESSION['last_move'] = 0;
        $_SESSION['error'] = '';
        $_SESSION['game_id'] = $this->createGame();
    }

    public function undo(): void
    {
        setState($this->db->undoMove($_SESSION['last_move']));
    }

}