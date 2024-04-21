# This is a sample Python script.

# Press ⌃R to execute it or replace it with your code.
# Press Double ⇧ to search everywhere for classes, files, tool windows, actions, and settings.
import time

class GameOfLife(object):
    generations: list = []

    def __init__(self):
        print('Game of Life')
        file_path = input('Enter file path: ')
        data = self.collect_file_data(file_path)
        self.generations.append(Generation(data))
        print(self.generations[-1])
        self.game_loop()
        print('Game finished')


    def collect_file_data(self, file_path: str):
        with open(file_path, 'r') as file:
            data = []
            for line in file:
                data.append([True if c == '1' else False for c in line.strip()])
        return data

    def game_loop(self):
        while self.continue_game():
            next_generation = self.generations[-1].next_generation()
            self.generations.append(next_generation)
            print(self.generations[-1], end='')
            time.sleep(0.5)

    def continue_game(self):
        return self.any_life_cell() and not self.has_repeat()

    def any_life_cell(self):
        return self.generations[-1].has_life()

    def has_repeat(self):
        for i in range(len(self.generations) - 1):
            if self.generations[i] == self.generations[-1]:
                return True
        return False


class Generation(object):
    dimension_x: int = 0
    dimension_y: int = 0
    field: list = []

    def __init__(self, field_array: list):
        self.dimension_x = len(field_array)
        self.dimension_y = len(field_array[0])
        self.field = []
        for i in range(self.dimension_x):
            self.field.append([])
            for j in range(self.dimension_y):
                cell = Cell(i, j, field_array[i][j], self.dimension_x, self.dimension_y)
                self.field[i].append(cell)

    def __eq__(self, other: 'Generation'):
        for i in range(self.dimension_x):
            for j in range(self.dimension_y):
                if self.field[i][j] != other.field[i][j]:
                    return False
        return True

    def next_generation(self):
        next_gen_list = []
        for i in range(self.dimension_x):
            next_gen_list.append([])
            for j in range(self.dimension_y):
                self.field[i][j].calculate_live_neighbors(self)
                next_gen_list[i].append(self.field[i][j].future_state())
        next_generation = Generation(next_gen_list)
        return next_generation

    def has_life(self):
        for i in range(self.dimension_x):
            for j in range(self.dimension_y):
                if self.field[i][j].state:
                    return True
        return False

    def __str__(self):
        result = '\r'
        for i in range(self.dimension_x):
            for j in range(self.dimension_y):
                result += '█ ' if self.field[i][j].state else '▢ '
            result += '\n'
        return result


class Cell(object):
    live_neighbors: int = 0
    state: bool = False
    x: int = 0
    y: int = 0
    top: int = 0
    bottom: int = 0
    left: int = 0
    right: int = 0

    def __init__(self, x: int, y: int, state: bool, field_x: int, field_y: int):
        self.x = x
        self.y = y
        self.state = state
        self.top = x - 1 if x - 1 >= 0 else field_x - 1
        self.bottom = x + 1 if x + 1 < field_x else 0
        self.left = y - 1 if y - 1 >= 0 else field_y - 1
        self.right = y + 1 if y + 1 < field_y else 0

    def calculate_live_neighbors(self, generation: 'Generation'):
        self.live_neighbors = 0
        if generation.field[self.top][self.left].state:
            self.live_neighbors += 1
        if generation.field[self.top][self.y].state:
            self.live_neighbors += 1
        if generation.field[self.top][self.right].state:
            self.live_neighbors += 1
        if generation.field[self.x][self.left].state:
            self.live_neighbors += 1
        if generation.field[self.x][self.right].state:
            self.live_neighbors += 1
        if generation.field[self.bottom][self.left].state:
            self.live_neighbors += 1
        if generation.field[self.bottom][self.y].state:
            self.live_neighbors += 1
        if generation.field[self.bottom][self.right].state:
            self.live_neighbors += 1

    def future_state(self):
        if self.state:
            if self.live_neighbors < 2 or self.live_neighbors > 3:
                return False
            return True
        return self.live_neighbors == 3

    def __eq__(self, other: 'Cell'):
        return self.state == other.state

    def __ne__(self, other: 'Cell'):
        return self.state != other.state

    def __str__(self):
        return '█' if self.state else '▢'



def print_hi(name):
    GameOfLife()


# Press the green button in the gutter to run the script.
if __name__ == '__main__':
    print_hi('PyCharm')

# See PyCharm help at https://www.jetbrains.com/help/pycharm/
