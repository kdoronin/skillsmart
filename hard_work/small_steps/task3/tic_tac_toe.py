## Игра "Крестики-нолики"
## Разработайте логику для игры "Крестики-нолики" на поле 3x3.


class TicTacToe:
    def __init__(self):
        self.board = [['', '', ''], ['', '', ''], ['', '', '']]

    def make_move(self, x, y, player):
        if x > 2 or y > 2:
            return None
        if self.board[x][y] != '':
            return None
        self.board[x][y] = player
        return self.board

    def check_winner(self):
        if self.check_lines() is not None:
            return self.check_lines()
        if self.check_columns() is not None:
            return self.check_columns()
        if self.check_diagonals() is not None:
            return self.check_diagonals()
        return None

    def check_lines(self):
        for line in self.board:
            if line[0] == line[1] == line[2] and line[0] != '':
                return line[0]

    def check_columns(self):
        for i in range(3):
            if self.board[0][i] == self.board[1][i] == self.board[2][i] and self.board[0][i] != '':
                return self.board[0][i]

    def check_diagonals(self):
        if self.board[0][0] == self.board[1][1] == self.board[2][2] and self.board[0][0] != '':
            return self.board[0][0]
        if self.board[0][2] == self.board[1][1] == self.board[2][0] and self.board[0][2] != '':
            return self.board[0][2]