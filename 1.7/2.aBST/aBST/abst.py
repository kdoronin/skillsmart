class aBST:

    def __init__(self, depth):
        # правильно рассчитайте размер массива для дерева глубины depth:
        self.tree_size = 2**(depth + 1) - 1
        self.Tree = [None] * self.tree_size  # массив ключей

    def FindKeyIndex(self, key):
        # ищем в массиве индекс ключа
        node = self.Tree[0]
        return self.find_key_index_step(key, 0)

    def find_key_index_step(self, key, index):
        if index >= self.tree_size:
            return None
        if self.Tree[index] is None:
            if index > 0:
                return -index
            else:
                return index
        else:
            if self.Tree[index] == key:
                return index
            elif key > self.Tree[index]:
                return self.find_key_index_step(key, 2*index + 2)
            else:
                return self.find_key_index_step(key, 2*index + 1)

    def get_left_child(self, index):
        child_index = 2*index + 1
        if child_index <= self.tree_size:
            return self.Tree[child_index]
        else:
            return None

    def get_right_child(self, index):
        child_index = 2 * index + 2
        if child_index <= self.tree_size:
            return self.Tree[child_index]
        else:
            return None

    def AddKey(self, key):
        # добавляем ключ в массив
        return self.add_one_step(key, 0)
        # индекс добавленного/существующего ключа или -1 если не удалось

    def add_one_step(self, key, index):
        if index >= self.tree_size:
            return -1
        if self.Tree[index] is None:
            self.Tree[index] = key
            return index
        elif key == self.Tree[index]:
            return index
        elif key > self.Tree[index]:
            return self.add_one_step(key, 2*index + 2)
        else:
            return self.add_one_step(key, 2*index + 1)