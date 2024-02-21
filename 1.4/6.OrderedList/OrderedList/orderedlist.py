class Node:
    def __init__(self, v):
        self.value = v
        self.prev = None
        self.next = None

class OrderedList:
    def __init__(self, asc):
        self.head = None
        self.tail = None
        self.__ascending = asc

    def compare(self, v1, v2):
        if v1 < v2:
            return -1
        elif v1 == v2:
            return 0
        else:
            return +1
        # -1 если v1 < v2
        # 0 если v1 == v2
        # +1 если v1 > v2

    def add(self, value):
        item = Node(value)
        if self.head is None:
            self.head = item
            item.prev = None
            item.next = None
            self.tail = item
        else:
            if self.__ascending:
                node = self.head
                while node is not None:
                    if self.compare(item.value, node.value) == -1 or self.compare(item.value, node.value) == 0:
                        if node == self.head:
                            item.next = node
                            node.prev = item
                            item.prev = None
                            self.head = item
                        else:
                            item.prev = node.prev
                            item.next = node
                            node.prev.next = item
                            node.prev = item
                        return
                    else:
                        node = node.next
                self.tail.next = item
                item.prev = self.tail
                item.next = None
                self.tail = item
                return
            else:
                node = self.tail
                while node is not None:
                    if self.compare(item.value, node.value) == -1 or self.compare(item.value, node.value) == 0:
                        if node == self.tail:
                            node.next = item
                            item.prev = node
                            item.next = None
                            self.tail = item
                        else:
                            item.next = node.next
                            item.prev = node
                            node.next.prev = item
                            node.next = item
                        return
                    else:
                        node = node.prev
                self.head.prev = item
                item.next = self.head
                item.prev = None
                self.head = item
                return
        # автоматическая вставка value
        # в нужную позицию

    def find(self, val):
        if self.head is None and self.tail is None:
            return
        if ((self.compare(val, self.head.value) == -1 or self.compare(val, self.tail.value) == +1) and self.__ascending) or ((self.compare(val, self.head.value) == +1 or self.compare(val, self.tail.value) == -1) and self.__ascending == False):
            return None # здесь будет ваш код
        else:
            node = self.head
            while node is not None:
                if self.__ascending:
                    if self.compare(val, node.value) == 0:
                        return node
                    elif self.compare(val, node.value) == -1:
                        return None
                    else:
                        node = node.next
                else:
                    if self.compare(val, node.value) == 0:
                        return node
                    elif self.compare(val, node.value) == +1:
                        return None
                    else:
                        node = node.next

    def delete(self, val):
        if self.head == None and self.tail == None:
            return
        if ((self.compare(val, self.head.value) == -1 or self.compare(val, self.tail.value) == +1) and self.__ascending) or ((self.compare(val, self.head.value) == +1 or self.compare(val, self.tail.value) == -1) and self.__ascending == False):
            return
        else:
            node = self.head
            while node is not None:
                if self.__ascending:
                    if self.compare(val, node.value) == 0:
                        if node == self.tail and node == self.head:
                            self.head = None
                            self.tail = None
                        elif node == self.tail:
                            node.prev.next = None
                            self.tail = node.prev
                        elif node == self.head:
                            node.next.prev = None
                            self.head = node.next
                        else:
                            node.prev.next = node.next
                            node.next.prev = node.prev
                        return
                    elif self.compare(val, node.value) == -1:
                        return
                    else:
                        node = node.next
                else:
                    if self.compare(val, node.value) == 0:
                        if node == self.tail and node == self.head:
                            self.head = None
                            self.tail = None
                        elif node == self.tail:
                            node.prev.next = None
                            self.tail = node.prev
                        elif node == self.head:
                            node.next.prev = None
                            self.head = node.next
                        else:
                            node.prev.next = node.next
                            node.next.prev = node.prev
                        return
                    elif self.compare(val, node.value) == +1:
                        return
                    else:
                        node = node.next

    def clean(self, asc):
        self.__ascending = asc
        self.head = None
        self.tail = None

    def len(self):
        node = self.head
        i = 0
        while node is not None:
            i = i + 1
            node = node.next
        return i

    def get_all(self):
        r = []
        node = self.head
        while node != None:
            r.append(node)
            node = node.next
        return r

class OrderedStringList(OrderedList):
    def __init__(self, asc):
        super(OrderedStringList, self).__init__(asc)

    def compare(self, v1, v2):

        if v1.strip() < v2.strip():
            return -1
        elif v1.strip() == v2.strip():
            return 0
        else:
            return +1