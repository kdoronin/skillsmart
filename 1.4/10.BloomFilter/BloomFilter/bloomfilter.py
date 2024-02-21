class BloomFilter:

    def __init__(self, f_len):
        self.filter_len = f_len
        self.filter = 0b0

        # создаём битовый массив длиной f_len ...

    def hash1(self, str1):
        # 17
        result = 0
        for c in str1:
            code = ord(c)
            result = (result * 17 + code) % self.filter_len
        return result
        # реализация ...

    def hash2(self, str1):
        # 223
        result = 0
        for c in str1:
            code = ord(c)
            result = (result * 223 + code) % self.filter_len
        return result
        # ...

    def add(self, str1):
        hash1 = 0b1 << self.hash1(str1)
        hash2 = 0b1 << self.hash2(str1)
        self.filter = self.filter | hash1
        self.filter = self.filter | hash2
        # добавляем строку str1 в фильтр

    def is_value(self, str1):
        hash1 = 0b1 << self.hash1(str1)
        hash2 = 0b1 << self.hash2(str1)
        if (self.filter | hash1) == self.filter and (self.filter | hash2) == self.filter:
            return True
        else:
            return False
        # проверка, имеется ли строка str1 в фильтре
