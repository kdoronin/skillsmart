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
        self.stackmain = Stack()
        self.stackhelp = Stack()

    def enqueue(self, item):
        self.stackmain.push(item)
        # вставка в хвост

    def dequeue(self):
        if self.size() > 0:
            while self.stackmain.size() > 0:
                self.stackhelp.push(self.stackmain.pop())
            output = self.stackhelp.pop()
            while self.stackhelp.size() > 0:
                self.stackmain.push(self.stackhelp.pop())
            return output
        else:
            return None  # если очередь пустая

    def size(self):
        return self.stackmain.size() # размер очереди