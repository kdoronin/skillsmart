class Heap:

    def __init__(self):
        self.HeapArray = []  # хранит неотрицательные числа-ключи
        self.size = None

    def MakeHeap(self, a, depth):
        # создаём массив кучи HeapArray из заданного
        # размер массива выбираем на основе глубины depth
        self.size = 2**(depth + 1) - 1
        self.HeapArray = [None] * self.size
        p = 0
        for i in a:
            self.insert_element(i, p)
            p += 1

    def insert_element(self, el, index):
        parent = self.get_parent(index)
        parent_index = (index - 1) // 2
        if parent is not None:
            if parent < el:
                self.HeapArray[index] = parent
                return self.insert_element(el, parent_index)
            else:
                self.HeapArray[index] = el
                return True
        else:
            if index == 0:
                self.HeapArray[index] = el
                return True
            else:
                return self.insert_element(el, parent_index)

    def get_parent(self, index):
        parent_index = (index - 1) // 2
        if parent_index >= 0:
            return self.HeapArray[parent_index]
        else:
            return None

    def get_left_child(self, index):
        child_index = 2*index + 1
        if child_index <= self.size:
            return self.HeapArray[child_index]
        else:
            return None

    def get_right_child(self, index):
        child_index = 2 * index + 2
        if child_index <= self.size:
            return self.HeapArray[child_index]
        else:
            return None

    def GetMax(self):
        # вернуть значение корня и перестроить кучу
        if self.HeapArray[0] is not None:
            old_head = self.HeapArray[0]
            self.HeapArray[0] = None
            if self.HeapArray[-1] is None:
                i = self.size - 1
                while self.HeapArray[i] is None and i > 0:
                    i -= 1
            else:
                i = self.size - 1
            last = self.HeapArray[i]
            self.HeapArray[i] = None
            if i != 0:
                self.set_new_root(last, 0)
            return old_head
        else:
            return -1  # если куча пуста

    def set_new_root(self, el, index):
        left_child = self.get_left_child(index)
        left_index = 2 * index + 1
        right_child = self.get_right_child(index)
        right_index = 2 * index + 2
        if left_child is not None and right_child is not None:
            if left_child > el and left_child > right_child:
                self.HeapArray[index] = left_child
                return self.set_new_root(el, left_index)
            elif right_child > el and right_child > left_child:
                self.HeapArray[index] = right_child
                return self.set_new_root(el, right_index)
        elif left_child is not None:
            if left_child > el:
                self.HeapArray[index] = left_child
                return self.set_new_root(el, left_index)
            elif right_child is not None:
                if right_child > el:
                    self.HeapArray[index] = right_child
                    return self.set_new_root(el, right_index)
        self.HeapArray[index] = el
        return True


    def Add(self, key):
        # добавляем новый элемент key в кучу и перестраиваем её
        if self.HeapArray[-1] is None:
            i = self.size - 1
            while self.HeapArray[i] is None:
                i -= 1
            return self.insert_element(key, i + 1)
        else:
            return False  # если куча вся заполнена