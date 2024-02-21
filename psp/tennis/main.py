# This is a sample Python script.

# Press ⌃R to execute it or replace it with your code.
# Press Double ⇧ to search everywhere for classes, files, tool windows, actions, and settings.
# 0, 15, 30, 40, 50


def print_hi(name):
    # Use a breakpoint in the code line below to debug your script.
    print(f'Hi, {name}')  # Press ⌘F8 to toggle the breakpoint.


class Player:
    score: int = 0
    name: str = ''
    deuce_score: int = 0
    is_winner: bool = False

    def __init__(self, name: str):
        self.name = name

    def increase_score(self):
        self.score += 1

    def increase_deuce(self):
        self.deuce_score += 1

    def make_winner(self):
        self.is_winner = True

    def get_score(self):
        return self.score

    def get_deuce_score(self):
        return self.deuce_score


def is_input_valid(who_won: str):
    if who_won not in ['1', '2']:
        print('Invalid input, please try again')
        return False
    return True


class Tennis:
    players = []
    is_deuce: bool = False
    is_finish: bool = False
    PLAYERS_COUNT: int = 2
    who_won: int = 0
    SCORE_MAP = {
        0: '0',
        1: '15',
        2: '30',
        3: '40',
        4: 'game'
    }

    def __init__(self):
        for i in range(self.PLAYERS_COUNT):
            player_name = input(f'Enter player {i + 1} name: ')
            self.players.append(Player(player_name))
        self.start_game()

    def start_game(self):
        while not self.is_finish:
            self.start_round()
            self.is_deuce = self.check_deuce()
            self.is_finish = self.check_finish()
        self.print_score()

    def start_round(self):
        who_won = input('Who won the round? (1/2): ')
        if not is_input_valid(who_won):
            return
        self.who_won = who_won
        if self.is_deuce:
            self.deuce_round()
            return
        self.normal_round()

    def normal_round(self):
        self.players[int(self.who_won) - 1].increase_score()

    def deuce_round(self):
        self.players[int(self.who_won) - 1].increase_deuce()

    def check_deuce(self) -> bool:
        if self.players[0].score == 3 and self.players[1].score == 3:
            return True

    def check_finish(self) -> bool:
        if self.players[0].score == 4 or self.players[1].score == 4:
            return True
        if self.is_deuce and self.deuce_diff() > 1:
            return True
        return False

    def deuce_diff(self):
        return abs(self.players[0].get_deuce_score() - self.players[1].get_deuce_score())

    def set_winner_normal(self):
        self.players[int(self.who_won) - 1].make_winner()

    def print_score(self):
        if self.is_deuce:
            print(f'Winner: {self.players[int(self.who_won) - 1].name}')
            print(f'Deuce score: {str(self.players[0].get_deuce_score())} - {str(self.players[1].get_deuce_score())}')
            return
        print(f'Winner: {self.players[int(self.who_won) - 1].name}')
        print(f'Score: {str(self.SCORE_MAP[self.players[0].get_score()])} - {str(self.SCORE_MAP[self.players[1].get_score()])}')



# Press the green button in the gutter to run the script.
if __name__ == '__main__':
    tennis = Tennis()

# See PyCharm help at https://www.jetbrains.com/help/pycharm/
