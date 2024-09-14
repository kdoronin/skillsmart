# Пример №1
В проекте в JavaScript есть функция `events`. Которая включает в себя всё, что нужно для взаимодействия с событиями.
Проблема в том, что часть функциональности логически не относится к событиям. 

В результате рефакторинга удалось выделить несколько переиспользуемых модулей:
1. Утилита для парсинга данных из JSON-объекта
2. Создал отдельный модуль для работы с атрибутами
3. Создал отдельный модуль для работы с чекбоксами

Как итог: размер исходного модуля `events` начал умещаться в комфортные для восприятия 70 строк, а в проекте появились
переиспользуемые модули для JS.

# Пример №2
Рефакторинг JS продолжается. Функция `addNewCustomPewcFields`, которая отвечает за добавление кастомного поля. Получили
мы её в наследство от отвратительного коммерческого плагина для кастомных полей.

Для начала, убрал из неё конфигурации полей в отдельный файл. Чтобы эту и другие конфигурации можно было отделить от
логики программы.

Добавил класс для работы с базовыми полями. Унаследовал от него класс для работы с чекбоксами из предыдущего примера.

# Пример №3
Третий модуль взаимодействия с полями, который нуждается в рефакторинге. `groupConditions` отвечает за сложную логику 
условий отображения полей. Которая может зависеть как от характеристик товара, так и от значений других полей.

Заменил функцию получения отдельного поля методом из соответствующего класса, который выделил в предыдущем примере.

Выделил в отдельный класс методы для работы с DOM. Чтобы классы полей через этот класс могли получать родительские или
соседние элементы в HTML.


# Про Use Cases
В проекте зачастую происходит такое, что какой-то из ключевых бизнес-процессов после релиза новой функциональности начинает 
работать медленно. Причём решается это замедление, как правило, достаточно небольшими правками.

В связи с чем сейчас (пока ещё в планах) хочу внедрить тесты на основные кейсы и их длительность работы.
## Со стороны пользователя (покупателя):
1. Положить товар в корзину
2. Открыть корзину с товарами
3. Открыть пустую корзину
4. Удалить товар из корзины
5. Открыть страницу чекаута
6. Перейти на этап оплаты заказа (здесь замеряется время создания заказа из объекта корзины)

## Со стороны внутреннего пользователя (редактора сайта):
1. Создание страницы
2. Создание поста в блоге
3. Создание продукта
4. Редактирование продукта:
   1. Нажатие кнопки Update без изменений
   2. Изменение цены вариации
   3. Изменение кастомных add-ons в продукте
   4. Изменение основной текстовой информации о продукте
5. Открыть список продуктов 20-50-200 штук

Идея заключается в том, чтобы не пропускать в релиз задачи, если они негативно влияют на данные метрики. Что позволит
лучше контролировать качество системы, которую мы поставляем как внутренним, так и внешним пользователям.