class Animal():
    def speak(self):
        pass

class Dog(Animal):
    def speak(self):
        print("Woof!")

class Cat(Animal):
    def speak(self):
        print("Meow!")

def animal_sound(animal: Animal):
    # Динамическое связывание. Будет вызван метод speak() того объекта, который был передан в качестве параметра
    return animal.speak()

cat = Cat()
dog = Dog()

animal_sound(cat) # Meow!
animal_sound(dog) # Woof!
