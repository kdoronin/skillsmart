<?php
//python не поддерживает ковариантность и контравариантность, поэтому в данном задании используется PHP


// Пример ковариантности. Параметризируем тип, возвращаемый родительским методом Adopt
// Возвращаемый тип метода Adopt в дочернем классе CatShelter может быть только подтипом возвращаемого типа метода Adopt в родительском классе AnimalShelter
class Animal
{
    public function sound(): string
    {
        return "Some sound";
    }
}

class Cat extends Animal
{
    public function sound(): string
    {
        return "Meow";
    }
}

class AnimalShelter
{
    public function adopt(): Animal
    {
        return new Animal;
    }
}

class CatShelter extends AnimalShelter
{
    public function adopt(): Cat
    {
        return new Cat;
    }
}

$animalShelter = new AnimalShelter();
$animal        = $animalShelter->adopt();
echo $animal->sound(); // Outputs: Some sound

$catShelter = new CatShelter();
$cat        = $catShelter->adopt();
echo $cat->sound(); // Outputs: Meow


// Пример контравариантности.
// Подтип заменяет

interface Eater {
    public function eat(Food $food);
}

class Food {
    public function name(): string {
        return "Some food";
    }
}

class Animal extends Food {
    public function name(): string {
        return "Generic animal";
    }
}

class CatEater implements Eater {
    public function eat(Animal $animal) {
        echo "Eating " . $animal->name();
    }
}

$catEater = new CatEater();

$food = new Food();
$animal = new Animal();

// This is valid since Animal is a subtype of Food
$catEater->eat($animal); // Outputs: Eating Generic animal

// This will cause a type error since we're expecting an Animal, not just any Food
// $catEater->eat($food);

