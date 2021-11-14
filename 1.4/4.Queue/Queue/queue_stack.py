class Stack:
    def __init__(self):
        self.stack = []

    def size(self):
        return len(self.stack)

    def pop(self):
        if self.size() > 0:
            return self.stack.pop(self.size() - 1)
        else:
            return None # если стек пустой

    def push(self, value):
        self.stack.append(value)

    def peek(self):
        if self.size() > 0:
            return self.stack[self.size() - 1]
        else:
            return None # если стек пустой

class Queue:
    def __init__(self):
        self.stackin = Stack()
        self.stackout = Stack()

    def enqueue(self, item):
        self.stackin.push(item)
        # вставка в хвост

    def dequeue(self):
        if self.size() > 0:
            if self.stackout.size() == 0:
                while self.stackin.size() > 0:
                    self.stackout.push(self.stackin.pop())
            return self.stackout.pop()
        else:
            return None  # если очередь пустая

    def size(self):
        return self.stackin.size() + self.stackout.size() # размер очереди