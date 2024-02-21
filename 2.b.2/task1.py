class Leg:
    def __init__(self, length):
        self.length = length


class Stool:
    def __init__(self, legs_count: int=3):
        self.legs = [Leg(10) for _ in range(legs_count)]

    def stay(self):
        print('Stool staying')

class Chair(Stool):
    def __init__(self):
        super().__init__(legs_count=4)

    def stay(self):
        print('Chair staying')

furniture = [Stool(), Chair()]
for item in furniture:
    item.stay()