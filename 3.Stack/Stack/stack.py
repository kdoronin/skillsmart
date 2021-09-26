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

def bracketsfunc(brackets: str):
    stack = Stack()
    print(len(brackets))
    for i in brackets:
        stack.push(i)
    sum = 0
    while stack.size() > 0:
        current = stack.pop()
        if current == ')':
            sum += 1
        elif current == '(' and sum >= 0:
            sum -= 1
        else:
            return 'Brackets are unbalanced'
    if sum == 0:
        return 'Brackets are balanced'
    else:
        return 'Brackets are unbalanced'

