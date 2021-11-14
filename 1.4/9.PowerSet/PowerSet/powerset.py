# наследуйте этот класс от HashTable
# или расширьте его методами из HashTable
class PowerSet:

    def __init__(self):
        self.dict = {}

    def size(self):
        return len(self.dict)
        # количество элементов в множестве

    def put(self, value):
        if not self.get(value):
            self.dict[value] = value
        # всегда срабатывает

    def get(self, value):
        # возвращает True если value имеется в множестве,
        # иначе False
        if value in self.dict:
            return True
        else:
            return False

    def remove(self, value):
        # возвращает True если value удалено
        # иначе False
        if self.get(value):
            self.dict.pop(value)
            return True
        else:
            return False

    def intersection(self, set2):
        # пересечение текущего множества и set2
        set3 = PowerSet()
        if self.size() < set2.size():
            for key in self.dict:
                if set2.get(key):
                    set3.put(key)
        else:
            for key in set2.dict:
                if self.get(key):
                    set3.put(key)
        return set3

    def union(self, set2):
        # объединение текущего множества и set2
        set3 = PowerSet()
        for key in self.dict:
            set3.put(key)
        for key in set2.dict:
            set3.put(key)
        return set3

    def difference(self, set2):
        # разница текущего множества и set2
        set3 = PowerSet()
        for key in self.dict:
            if not set2.get(key):
                set3.put(key)
        return set3

    def issubset(self, set2):
        # возвращает True, если set2 есть
        # подмножество текущего множества,
        # иначе False
        for key in set2.dict:
            if not self.get(key):
                return False
        return True