class Node:
    def __init__(self, v):
        self.value = v
        self.prev = None
        self.next = None

class LinkedList2:  
    def __init__(self):
        self.head = None
        self.tail = None

    def add_in_tail(self, item):
        if self.head is None:
            self.head = item
            item.prev = None
            item.next = None
        else:
            self.tail.next = item
            item.prev = self.tail
        self.tail = item

    def find(self, val):
        node = self.head
        while node is not None:
            if node.value == val:
                return node
            node = node.next
        return None
    
    def print_all_nodes(self):
        node = self.head
        while node != None:
            print(node.value)
            node = node.next

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
        while node is not None:
            if node.value == val:
                if node == self.head and node == self.tail:
                    self.head = None
                    self.tail = None
                    return
                elif node == self.head:
                    self.head = node.next
                    node.next.prev = self.head
                    if all == False:
                        return
                    else:
                        node = node.next
                elif node == self.tail:
                    node.prev.next = None
                    self.tail = node.prev
                    node = node.next
                else:
                    node.prev.next = node.next
                    node.next.prev = node.prev
                    if all == False:
                        return
                    else:
                        node = node.next
            else:
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
                    newNode.prev = node
                    newNode.next = node.next
                    node.next = newNode
                    return
                node = node.next
        elif self.head == None:
            self.head = newNode
            newNode.prev = None
            newNode.next = None
            self.tail = newNode
        else:
            self.tail.next = newNode
            newNode.prev = self.tail
            self.tail = newNode
            newNode.next = None
        return

    def add_in_head(self, newNode):
        if self.head is None:
            self.head = newNode
            newNode.prev = None
            newNode.next = None
            self.tail = newNode
        else:
            self.head.prev = newNode
            newNode.next = self.head
            self.head = newNode


def test_function():
    find_list = LinkedList2()
    finded_node = find_list.find(1)
    find_list.print_all_nodes()
    print(finded_node)
    print('---------------------')
    find_list.add_in_tail(Node(1))
    finded_node2 = find_list.find(2)
    find_list.print_all_nodes()
    print(finded_node2)
    print('---------------------')
    finded_node3 = find_list.find(1)
    find_list.print_all_nodes()
    print(finded_node3.value)
    print('---------------------')
    find_list.add_in_tail(Node(8))
    find_list.add_in_tail(Node(4))
    find_list.add_in_tail(Node(7))
    find_list.add_in_tail(Node(1))
    find_list.add_in_tail(Node(1))
    finded_node4 = find_list.find(1)
    finded_node5 = find_list.find(8)
    finded_node6 = find_list.find(7)
    print(finded_node4.value)
    print(finded_node5.value)
    print(finded_node6.value)
    find_list.print_all_nodes()
    print('---------------------')
    find_list.delete(3, False)
    find_list.delete(8, False)
    find_list.delete(4, False)
    find_list.delete(1, True)
    find_list.print_all_nodes()
    print(find_list.head.value)
    print(find_list.tail.value)
    print('---------------------')

    
test_function()