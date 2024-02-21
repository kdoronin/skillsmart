class Animal:
    def speak(self):
        return "This can speak!"


class Dog(Animal):
    def woof(self):
        return "Woof!"


class Cat(Animal):
    def speak(self):
        return "Meow"


class Cow(Animal):
    def speak(self):
        return "Moo"


animals = [Dog(), Cat(), Cow()]

for animal in animals:
    print(animal.speak())


# Ковариантный вызов.

from typing import Generic, TypeVar, Callable

animal = TypeVar('animal', covariant=True)

class Animal():
    def make_sound(self):
        raise NotImplementedError

class Cat(Animal):
    def make_sound(self):
        print('Meow!')

class Dog(Animal):
    def make_sound(self):
        print('Woof!')

# ящик на вход принимает любой объект типа Animal
class Box(Generic[animal]):
    def __init__(self, content: animal) -> None:
        self._content = content

    def make_sound(self):
        self._content.make_sound()

# Ковариантный вызов:
def shake_box(box: Box[Animal]):
    box.make_sound()

some_animal = Cat()
box = Box(some_animal)
shake_box(box)