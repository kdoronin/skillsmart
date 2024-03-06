import unittest

from tic_tac_toe import TicTacToe


class TestTicTacToe(unittest.TestCase):
    def test_start_game(self):
        game = TicTacToe()
        self.assertEqual(game.board, [['', '', ''], ['', '', ''], ['', '', '']])

    def test_make_move(self):
        game = TicTacToe()
        game.make_move(0, 0, 'X')
        game.make_move(1, 1, 'O')
        self.assertEqual(game.board, [['X', '', ''], ['', 'O', ''], ['', '', '']])

    def test_make_move_2(self):
        game = TicTacToe()
        game.make_move(0, 0, 'X')
        game.make_move(1, 1, 'O')
        game.make_move(2, 2, 'X')
        self.assertEqual(game.board, [['X', '', ''], ['', 'O', ''], ['', '', 'X']])

    def test_win(self):
        game = TicTacToe()
        game.make_move(0, 0, 'X')
        game.make_move(1, 0, 'O')
        game.make_move(0, 1, 'X')
        game.make_move(1, 1, 'O')
        game.make_move(0, 2, 'X')
        self.assertEqual(game.check_winner(), 'X')

    def test_win_2(self):
        game = TicTacToe()
        game.make_move(0, 0, 'X')
        game.make_move(1, 0, 'O')
        game.make_move(0, 1, 'X')
        game.make_move(1, 1, 'O')
        game.make_move(2, 2, 'X')
        game.make_move(1, 2, 'O')
        self.assertEqual(game.check_winner(), 'O')

    def test_win_3(self):
        game = TicTacToe()
        game.make_move(0, 0, 'X')
        game.make_move(0, 1, 'O')
        game.make_move(1, 1, 'X')
        game.make_move(0, 2, 'O')
        self.assertEqual(game.check_winner(), None)

    def test_move_outside(self):
        game = TicTacToe()
        self.assertEqual(game.make_move(0, 3, 'X'), None)

    def test_move_not_empty(self):
        game = TicTacToe()
        game.make_move(0, 0, 'X')
        self.assertEqual(game.make_move(0, 0, 'O'), None)
