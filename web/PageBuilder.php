<?php
include_once 'DBO.php';

class PageBuilder
{

    private $db;
    private $game;

    public function __construct($game)
    {
        $this->game = $game;
        $this->db = new DBO();
    }

    public function printMoveHistory(){
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE game_id = '.$this->game->getGameId());
        if ($stmt === false) {
            return;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_array()) {
            echo '<li>'.$row[2].' '.$row[3].' '.$row[4].'</li>';
        }
    }

    public function printHand($player){
        foreach ($this->game->getHand()[$player] as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player'.$player.'"><span>'.$tile."</span></div> ";
            }
        }
    }

    public function printBoard(){
        $min_p = 1000;
        $min_q = 1000;
        foreach ($this->game->getBoard() as $pos => $tile) {
            $pq = explode(',', $pos);
            if ($pq[0] < $min_p) {
                $min_p = $pq[0];
            }
            if ($pq[1] < $min_q) {
                $min_q = $pq[1];
            }
        }
        foreach (array_filter($this->game->getBoard()) as $pos => $tile) {
            $pq = explode(',', $pos);
            $pq[0];
            $pq[1];
            $h = count($tile);
            echo '<div class="tile player';
            echo $tile[$h-1][0];
            if ($h > 1) echo ' stacked';
            echo '" style="left: ';
            echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
            echo 'em; top: ';
            echo ($pq[1] - $min_q) * 4;
            echo "em;\">($pq[0],$pq[1])<span>";
            echo $tile[$h-1][1];
            echo '</span></div>';
        }
    }

    public function printMoveTo(){
        foreach ($this->game->getMovesTo() as $pos) {
            echo "<option value=\"$pos\">$pos</option>";
        }
    }

    public function printMoveFrom(){
        foreach (array_filter($this->game->getBoard()) as $pos => $tile) {
            $h = count($tile);
            if ($tile[$h-1][0] == $this->game->getPlayer()){
                echo "<option value=\"$pos\">$pos</option>";
            }
        }
    }

    public function printPlayTo(){
        foreach ($this->game->getMovesTo() as $pos) {
            if (empty($this->game->getBoard())){
                echo "<option value=\"$pos\">$pos</option>";
            }
            elseif(!in_array($pos, $this->game->getBoard())) {
                echo "<option value=\"$pos\">$pos</option>";
            }
        }
    }

    public function printPiecesAvailable(){
        foreach ($this->game->getHand()[$this->game->getPlayer()] as $tile => $ct) {
            if ($ct > 0) {
                echo "<option value=\"$tile\">$tile</option>";
            }
        }
    }
}