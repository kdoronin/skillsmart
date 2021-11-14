class NativeCache:
    def __init__(self, sz):
        self.size = sz
        self.slots = [None] * self.size
        self.values = [None] * self.size
        self.hits = [0] * self.size

    def hash_fun(self, value):
        # в качестве value поступают строки!
        index = len(value) % self.size
        # всегда возвращает корректный индекс слота
        return index

    def is_key(self, key):
        # возвращает True если ключ имеется,
        index = self.hash_fun(key)
        if self.slots[index] == key:
            return True
        else:
            i = 0
            while i < self.size:
                index += 1
                if index >= self.size:
                    index = index - self.size
                if self.slots[index] == key:
                    return True
                elif self.slots[index] is None:
                    return False
                else:
                    i += 1
            return False

    def put(self, key, value):
        index = self.hash_fun(key)
        if self.slots[index] is None:
            self.slots[index] = key
            self.values[index] = value
            return
        else:
            i = 0
            while i < self.size:
                index += 1
                if index >= self.size:
                    index = index - self.size
                if self.slots[index] is None:
                    self.slots[index] = key
                    self.values[index] = value
                    return
                else:
                    i += 1
            lst_num = list(enumerate(self.hits, 0))
            t_min = min(lst_num, key=lambda i: i[1])
            self.slots[t_min[0]] = key
            self.values[t_min[0]] = value
            self.hits[t_min[0]] = 0
            return

        # гарантированно записываем
        # значение value по ключу key

    def get(self, key):
        # возвращает value для key,
        # или None если ключ не найден
        index = self.hash_fun(key)
        if self.slots[index] == key:
            self.hits[index] += 1
            return self.values[index]
        else:
            i = 0
            while i < self.size:
                index += 1
                if index >= self.size:
                    index = index - self.size
                if self.slots[index] == key:
                    self.hits[index] += 1
                    return self.values[index]
                elif self.slots[index] is None:
                    return None
                else:
                    i += 1
            return None