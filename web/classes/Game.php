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
            $this->board = new Board([], $this->db);
            $this->currentPlayer = 0;
            $this->players = [new Player(0), new Player(1)];
            $this->hand = [0 => $this->players[0]->getHandArray(), 1 => $this->players[1]->getHandArray()];
            $_SESSION['board'] = $this->board->getBoard();
            $_SESSION['player'] = $this->players[$this->currentPlayer]->getPlayer();
            $_SESSION['game_id'] = $this->game_id;
            $_SESSION['hand'] = [0 => $this->players[0]->getHandArray(), 1 => $this->players[1]->getHandArray()];
            $_SESSION['last_move'] = 0;
        }
        else {
            $this->game_id = $_SESSION['game_id'];
            $this->players = [new Player(0, $_SESSION['hand'][0]), new Player(1, $_SESSION['hand'][1])];
            $this->board = new Board($_SESSION['board'], $this->db);
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

    public function getBoardObject(): Board
    {
        return $this->board;
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
     * @return int
     */
    public function getGameId(): int
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
                if (count($this->board->getBoard()) && !$this->board->hasNeighbour($loc, $this->board->getBoard())) {
                    continue;
                }
                if ($this->len($this->players[$this->currentPlayer]->getHandArray()) < 11 && !$this->board->neighboursAreSameColor($this->currentPlayer, $loc, $this->board->getBoard())) {
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
        $handArray = $this->players[$player]->getHandArray();

        $this->board->play($piece, $to, $player, $handArray, $this->players);
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
        $hand = $_SESSION['hand'][$player];

        unset($_SESSION['error']);

        $this->board->move($from, $to, $player, $hand);
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
        $this->board->setState($this->db->undoMove($_SESSION['last_move']));
    }

    function len($tile): int
    {
        return $tile ? count($tile) : 0;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }
}