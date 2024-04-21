## Истинное наследование

### Описание
За основу взял плагин мультиязычности, с которого как раз сейчас переезжаю на основном проекте.
Здесь 4 класса, реализующих страницы настроек. У каждой страницы отличается поведение при сборе настроек,
а также при сохранении данных из полей. Дочерние классы переопределяют методы родительского класса, что как раз подходит
под условие поставленной задачи.

В качестве примера переведу в Visitor метод для получения настроек. Также в другой паттерн можно перевести метод `save`.
Как я понимаю, в visitor необходимо соблюдать однородность возвращаемых методами значений

### Выводы
Реализовал паттерн, перенеся в него логику получения настроек, реализовав разные методы, в зависимости от страниц.
В моём примере паттерн не очень хорошо раскрывается, но он был бы очень полезен в случае, если настройки на страницу можно
притащить не только из базы данных, но и из файлов или, например, из очереди. 

Также, по всей видимости, стоит перенести сохранение в другой Visitor, чтобы логика работала и в другую сторону.

Сам по себе факт того, что вместо кастомного решения (а плагин, из которого взяты классы, изобилует сомнительными кастомными
решениями) используется широко известный паттерн, позволяет решению быть более понятным для других разработчиков, а также
даёт больше простора для расширения функциональности.