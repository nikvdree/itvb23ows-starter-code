<?php
    session_start();

    include_once 'util.php';
    include_once 'Game.php';
    include_once 'PageBuilder.php';

    $game = new Game();
    $pageBuilder = new PageBuilder($game);
    $WHITE = 0;
    $BLACK = 1;

    $to = $game->getMovesTo();
?>
<!DOCTYPE html>
<html lang="utf-8">
    <head>
        <title>Hive</title>
        <link rel="stylesheet" href="../style/style.css">
    </head>
    <body>
        <div class="board">
            <?php
                $pageBuilder->printBoard();
            ?>
        </div>
        <div class="hand">
            White:
            <?php
                $pageBuilder->printHand($WHITE);
            ?>
        </div>
        <div class="hand">
            Black:
            <?php
                $pageBuilder->printHand($BLACK);
            ?>
        </div>
        <div class="turn">
            Turn:
            <?php if ($game->getPlayer() == $WHITE) {
                echo "White";
            } else {
                echo "Black";
            } ?>
        </div>
        <form method="post" action="play.php">
            <select name="piece">
                <?php
                    $pageBuilder->printPiecesAvailable();
                ?>
            </select>
            <select name="to">
                <?php
                    $pageBuilder->printPlayTo();
                ?>
            </select>
            <input type="submit" value="Play">
        </form>
        <form method="post" action="move.php">
            <select name="from">
                <?php
                    $pageBuilder->printMoveFrom();
                ?>
            </select>
            <select name="to">
                <?php
                    $pageBuilder->printMoveTo();
                ?>
            </select>
            <input type="submit" value="Move">
        </form>
        <form method="post" action="pass.php">
            <input type="submit" value="Pass">
        </form>
        <form method="post" action="restart.php">
            <input type="submit" value="Restart">
        </form>
        <strong>
            <?php if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            }
            ?>
        </strong>
        <ol>
            <?php
                $pageBuilder->printMoveHistory();
            ?>
        </ol>
        <form method="post" action="undo.php">
            <input type="submit" value="Undo">
        </form>
    </body>
</html>

