class Node:

    def __init__(self, v):
        self.value = v
        self.next = None

class LinkedList:

    def __init__(self):
        self.head = None
        self.tail = None

    def add_in_tail(self, item):
        if self.head is None:
            self.head = item
        else:
            self.tail.next = item
        self.tail = item

    def print_all_nodes(self):
        node = self.head
        while node != None:
            print(node.value)
            node = node.next

    def find(self, val):
        node = self.head
        while node is not None:
            if node.value == val:
                return node
            node = node.next
        return None

    def find_all(self, val):
        OutputList = list()
        node = self.head
        while node is not None:
            if node.value == val:
                OutputList.append(node)
            node = node.next
        return OutputList

    def delete(self, val, all=False):
        node = self.head
        node_prev = self.head
        while node is not None:
            if node.value == val:
                if node == self.head and node == self.tail:
                    self.head = None
                    self.tail = None
                    return
                elif node == self.head:
                    self.head = node.next
                    if all == False:
                        return
                elif node == self.tail:
                    node_prev.next = None
                    self.tail = node_prev
                else:
                    node_prev.next = node.next
                    if all == False:
                        return
            if node == self.head:
                node = node.next
            else:
                node_prev = node
                node = node.next

    def clean(self):
        self.head = None
        self.tail = None

    def len(self):
        node = self.head
        i = 0
        while node is not None:
            i = i + 1
            node = node.next
        return i

    def insert(self, afterNode, newNode):
        if afterNode != None:
            node = self.head
            while node is not None:
                if node == afterNode:
                    if node == self.tail:
                        self.tail = newNode
                    newNode.next = node.next
                    node.next = newNode
                    return
                node = node.next
        else:
            newNode.next = self.head.next
            self.head = newNode
        return
def CustomFunc(item1: LinkedList, item2: LinkedList):
    if item1.len() == item2.len():
        new_list = LinkedList()
        node = item1.head
        node2 = item2.head
        while node is not None:
            value_sum = node.value + node2.value
            new_list.add_in_tail(Node(value_sum))
            node = node.next
            node2 = node2.next
        return new_list