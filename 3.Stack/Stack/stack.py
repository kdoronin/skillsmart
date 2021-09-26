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

def bracketsfunc(stack: Stack):
    sum = 0
    while stack.size() > 0:
        if stack.peek() == ')':
            sum += 1
        elif stack.peek() == '(' and sum >= 0:
            sum -= 1
        else:
            return False
        stack.pop()
    return sum == 0