class Deque:
    def __init__(self):
        self.deque = []
        # инициализация внутреннего хранилища

    def addFront(self, item):
        self.deque.insert(0, item)
        # добавление в голову

    def addTail(self, item):
        self.deque.append(item)
        # добавление в хвост

    def removeFront(self):
        # удаление из головы
        if self.size() > 0:
            return self.deque.pop(0)
        else:
            return None # если очередь пуста

    def removeTail(self):
        # удаление из хвоста
        if self.size() > 0:
            return self.deque.pop(self.size() - 1)
        else:
            return None # если очередь пуста

    def size(self):
        return len(self.deque) # размер очереди

def palindromeCheck(inputString: list):
    de = Deque()
    for item in inputString:
        de.addFront(item)
    while de.size() > 1:
        if de.removeFront() != de.removeTail():
            return False
    return True