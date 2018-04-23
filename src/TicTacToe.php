<?php

namespace kb;
use kb\exceptions\FieldOccupiedException;
use kb\exceptions\SymbolTakenException;

class TicTacToe
{
    private $board = [];
    private $players = [];
    private $size;
    private $statusMessage = 'Game is ongoing';
    private $isRunning = true;

    const COMPUTER_KEY = 'computer';

    /**
     * TicTacToe constructor.
     * @param int $size dimensions of the square
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct($size, array $options)
    {
        if (!in_array($size, range(3, 10))) {
            throw new \InvalidArgumentException("Invalid size $size, must be between 3 and 10");
        }
        $this->size = (int) $size;
        foreach ($options['players'] as $name => $symbol) {
            $this->addPlayer($name, $symbol);
        }
        if (empty($options['players'][static::COMPUTER_KEY])) {
            throw new \InvalidArgumentException(sprintf("Key %s is missing in options array", static::COMPUTER_KEY));
        }

        // prepare board
        for ($i = 1; $i <= $this->size; $i++) {
            for ($j = 1; $j <= $this->size; $j++) {
                $this->board[$i][$j] = '';
            }
        }

        // shuffle running order
        $playerNames = array_keys($this->players);
        shuffle($playerNames);
        $runningOrder = [];
        foreach ($playerNames as $playerName) {
            $runningOrder[$playerName] = $this->players[$playerName];
        }
        $this->players = $runningOrder;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->isRunning;
    }

    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function nextPlayer()
    {
        $nextPlayer = next($this->players);
        if (!$nextPlayer) {
            reset($this->players);
        }
        return key($this->players);
    }

    public function computerMove()
    {
        foreach ($this->board as $x => $columns) {
            foreach ($columns as $y => $symbol) {
                if (empty($symbol)) {
                    $this->place(static::COMPUTER_KEY, $x, $y);
                    return;
                }
            }
        }
    }

    /**
     * @param string $playerName
     * @param int $x
     * @param int $y
     * @throws Exception
     */
    public function place($playerName, $x, $y)
    {
        static $moveCount = 0;
        if (!array_key_exists($playerName, $this->players)) {
            throw new \Exception("Can not identify player $playerName");
        }

        if (!array_key_exists($x, $this->board) || !array_key_exists($y, $this->board[$x])) {
            throw new \OutOfBoundsException("Field is out of bounds");
        }
        if (!empty($this->board[$x][$y])) {
            throw new FieldOccupiedException("Field $x,$y is already occupied");
        }
        $this->board[$x][$y] = $this->players[$playerName];
        $moveCount++;
        if ($moveCount >= $this->size ** 2) {
            $this->isRunning = false;
            $this->statusMessage = "Board is full without winner";
            return;
        }

        $winner = $this->getWinner();
        if (!empty($winner)) {
            $this->isRunning = false;
            $this->statusMessage = "We have a winner: $winner";
            return;
        }
    }

    public function printBoard()
    {
        echo PHP_EOL;
        foreach ($this->board as $x => $columns) {
            if (empty($headers)) {
                echo '  ';
                foreach (array_keys($columns) as $y) {
                    echo "|" . str_pad($y, 2);
                }
                echo PHP_EOL;
                $headers = true;
            }
            echo str_repeat('-', 2 + 3 * count($columns)) . PHP_EOL;
            echo str_pad($x, 2);
            foreach ($columns as $y => $symbol) {
                echo $symbol ? "|$symbol ": '|  ';
            }
            echo PHP_EOL;
        }
    }

    /**
     * @param string $name
     * @param string $symbol
     * @throws SymbolTakenException
     */
    private function addPlayer($name, $symbol)
    {
        if (false !== array_search($symbol, $this->players)) {
            throw new SymbolTakenException("symbol $symbol already taken");
        }

        $this->players[$name] = $symbol;
    }

    /**
     * @return null|string
     */
    private function getWinner()
    {
        $winningSymbol = null;
        foreach ($this->board as $x => $columns) {
            foreach ($columns as $y => $symbol) {
                // check NORTH + SOUTH neighbor
                if (!empty($this->board[$x - 1][$y]) && !empty($this->board[$x + 1][$y])) {
                    $northSymbol = $this->board[$x - 1][$y];
                    $southSymbol = $this->board[$x + 1][$y];
                    if ($southSymbol === $symbol && $northSymbol === $symbol) {
                        $winningSymbol = $symbol;
                        break 2;
                    }
                }

                // check WEST + EAST neighbor
                if (!empty($this->board[$x][$y - 1]) && !empty($this->board[$x][$y + 1])) {
                    $westSymbol = $this->board[$x][$y - 1];
                    $eastSymbol = $this->board[$x][$y + 1];
                    if ($westSymbol === $symbol && $eastSymbol === $symbol) {
                        $winningSymbol = $symbol;
                        break 2;
                    }
                }

                // check NORTHWEST + SOUTHEAST neighbor
                if (!empty($this->board[$x - 1][$y - 1]) && !empty($this->board[$x + 1][$y + 1])) {
                    $northWestSymbol = $this->board[$x - 1][$y - 1];
                    $southEastSymbol = $this->board[$x + 1][$y + 1];
                    if ($northWestSymbol === $symbol && $southEastSymbol === $symbol) {
                        $winningSymbol = $symbol;
                        break 2;
                    }
                }

                // check SOUTHWEST + NORTHEAST neighbor
                if (!empty($this->board[$x + 1][$y - 1]) && !empty($this->board[$x - 1][$y + 1])) {
                    $southWestSymbol = $this->board[$x + 1][$y - 1];
                    $northEastSymbol = $this->board[$x - 1][$y + 1];
                    if ($southWestSymbol === $symbol && $northEastSymbol === $symbol) {
                        $winningSymbol = $symbol;
                        break 2;
                    }
                }
            }
        }
        if (empty($winningSymbol)) {
            return null;
        }
        return array_search($winningSymbol, $this->players);
    }
}
