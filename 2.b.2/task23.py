# 1. Если мы сделаем у класса Транспортное средство дочерний класс Велосипед, то для метода "двигаться" нам может понадобиться
# добавить предусловие "наличие колёс", что ограничивает метод родительского класса. Это нарушение.
# 2. Базовый класс – "Учебный курс". У него есть метод "Пройти курс" с постусловием "учащийся изучил тему". Класс-наследник
# "Учебный онлайн-курс", у которого постусловие метода "Пройти курс" может быть ослаблена до "учащийся ознакомился с темой"
# это является ослаблением постусловия, что нарушает принцип подстановки.