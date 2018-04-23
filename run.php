<?php

require __DIR__ . '/vendor/autoload.php';

use kb\TicTacToe;

$options = parse_ini_file('configs/players.ini', true);

$valid = false;
while (!$valid) {
    echo "What's the field size you prefer? (any from 3 to 10)\n";
    $size = trim(fgets(STDIN));
    try {
        $game = new TicTacToe($size, $options);
        $valid = true;
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        $valid = false;
    }
}

$game->printBoard();
while ($game->isRunning()) {

    $nextPlayer = $game->nextPlayer();
    if ('computer' === $nextPlayer) {
        $game->computerMove();
        $game->printBoard();
        $nextPlayer = $game->nextPlayer();
    }

    $valid = false;
    while (!$valid) {
        echo "$nextPlayer, what's the next field? (format 1,2)\n";
        $field = trim(fgets(STDIN));
        if (false === strpos($field, ',')) {
            echo "wrong format\n";
            continue;
        }
        list($x, $y) = explode(',', $field);
        try {
            $game->place($nextPlayer, $x, $y);
            $game->printBoard();
            $valid = true;
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            $valid = false;
        }
    }

}
echo $game->getStatusMessage() . PHP_EOL;
