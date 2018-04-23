<?php

namespace kb;

class TicTacToeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return TicTacToe
     */
    public function testGameIsRunning()
    {
        $game = new TicTacToe(3, [
            'players' => [
                'P1' => 'X',
                'P2' => 'O',
                'computer' => 'C',
            ],
        ]);
        $this->assertTrue($game->isRunning());
        return $game;
    }

    /**
     * @depends testGameIsRunning
     * @param TicTacToe $game
     */
    public function testWinner(TicTacToe $game)
    {
        $game->place('P1', 1, 1);
        $this->assertTrue($game->isRunning());

        $game->place('P1', 1, 2);
        $this->assertTrue($game->isRunning());

        $game->place('P1', 2, 2);
        $this->assertTrue($game->isRunning());

        // winning move
        $game->place('P1', 3, 3);
        $this->assertFalse($game->isRunning());
        $this->assertSame("We have a winner: P1", $game->getStatusMessage());
    }

    /**
     * @depends testGameIsRunning
     * @param TicTacToe $game
     * @expectedException \kb\exceptions\FieldOccupiedException
     */
    public function testFieldIsOccupied(TicTacToe $game)
    {
        $game->place('P1', 1, 3);
        $game->place('P1', 1, 3);
    }
}
