# Наследования вариаций
class Human:
    def greet(self):
        print("Hello!")

class Man(Human):
    def greet(self):
        print("Hello, I am a man.")

class Woman(Human):
    def greet(self):
        print("Hello, I am a woman.")

# Наследование с конкретизацией

from abc import ABC, abstractmethod

class Vehicle(ABC):
    @abstractmethod
    def move(self):
        pass

class Car(Vehicle):
    def move(self):
        print("Car is moving.")

class Airplane(Vehicle):
    def move(self):
        print("Airplane is flying.")


# Структурное наследование

from abc import ABC, abstractmethod

class Iterable(ABC):
    @abstractmethod
    def __iter__(self):
        pass

class MyList(Iterable):
    def __init__(self, items):
        self._items = items

    def __iter__(self):
        return iter(self._items)

my_list = MyList([1, 2, 3])
for item in my_list:
    print(item)
