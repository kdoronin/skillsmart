 class Apple:
    def __init__(self, w, c, s, m):
        self.weight = w
        self.color = c
        self.size = s
        self.mold = m
        print("Created!")

# Расширяет класс Apple
class Fruit(Apple):
    def __init__(self, w, c, s, m, t):
        super().__init__(w, c, s, m)
        self.taste = t
        print("Created!")

# Специализирует класс Apple
class RedApple(Apple):
    def __init__(self, w, c, s, m, t, r):
        super().__init__(w, c, s, m)
        self.taste = t
        self.red = r
        print("Created!")