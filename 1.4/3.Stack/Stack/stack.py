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

def bracketsfunc(string: str):
    stack = Stack()
    for i in string:
        if i == '(':
            stack.push(i)
        elif i == ')' and stack.size() > 0:
            stack.pop()
        else:
            return False
    return stack.size() == 0