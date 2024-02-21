class Vehicle:
    def __init__(self, wheels, max_speed):
        self.wheels = wheels
        self.max_speed = max_speed

    def move(self):
        print("Vehicle move")


class Car(Vehicle):
    def __init__(self, wheels, max_speed, doors):
        super().__init__(wheels, max_speed)
        self.doors = doors

    def move(self):
        print("Driving a car")


class Bicycle(Vehicle):
    def __init__(self, wheels, max_speed):
        super().__init__(wheels, max_speed)

    def move(self):
        print("Riding a bicycle")


class RoadBike(Bicycle):
    def __init__(self, wheels, max_speed, frameset):
        super().__init__(wheels, max_speed)
        self.frameset = frameset
