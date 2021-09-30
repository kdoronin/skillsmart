class Queue:
    def __init__(self):
        self.queue = []
        # инициализация хранилища данных

    def enqueue(self, item):
        self.queue.append(item)
        # вставка в хвост

    def dequeue(self):
        # выдача из головы
        if self.size() > 0:
            return self.queue.pop(0)
        else:
            return None # если очередь пустая

    def size(self):
        return len(self.queue)

    def rotateQueue(self, N: int):
        for i in range(N):
            self.enqueue(self.dequeue())
            i += 1

