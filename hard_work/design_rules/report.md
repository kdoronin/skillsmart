# Отчёт

## Описание выбранных примеров
Для анализа по примерам я выбрал плагин для интеграции с Google Analytics 4, который писал сам. Я его писал как раз под
вдохновением от Object Calisthenics. И пытался соответствовать тем правилам, что в описаны в инструкции к данному методу
разработки.

### Пример 1.1
Один из примеров, когда CMS подкладывает разработчикам "свинью". В данном случае это action-ы, которые существуют где-то
в исходном плагине. При этом там, как правило, нет никакой жёсткой привязки к типам данных, которые передаются вместе с
конкретным хуком. Более того – любой другой разработчик может подключиться к хуку с более высоким приоритетом и переопределить
корректные данные чем угодно. В моём примере явно не хватает проверки на те значения, которые я получаю из хука.

### Пример 1.2
В конструкторе абстрактного класса я использую в качестве второго параметра массив `$params`. Соответственно, если разработчик
не знает, какие именно параметры должны быть у конкретного события, он склонен к тому, чтобы допустить ошибку. Такое поведение
программы не является правильным.

### Пример 2.1
В данном примере я атрибут `ID` для объекта `User` получаю уже внутри объекта. Для моего кейса это пока что работает, так
как я работаю с текущим пользователем (если он авторизован), либо у пользователя будет ID `0`. Но в общем случае такого
лучше избегать. Потому что даже в моём кейсе может потребоваться отправлять события с разными пользователями. Например,
если текущий пользователь инициировал какое-то действие в отношении другого пользователя и нужно отправить два события с
разными данными пользователей: один `send_request`, другой `accept_request`.

### Пример 2.2
Аналогичная примеру 2.1 ситуация. Есть объект `Session`, `ID` которого задаётся внутри объекта. Тут варианта, на самом деле,
два: либо передавать ID извне, либо сделать `Session` синглтоном. И в таком случае, думаю, объект может существовать без
параметров в конструкторе.

### Пример 3.1
В этом примере на вход конструктору передаётся массив `$data`. По сути, это специальным-образом организованные данные событий,
объединённые в массив. Проблема в том, что сам по себе массив `$data` в таком случае вообще никак не ограничен на данные
внутри. Самое интересное здесь то, что у меня уже есть классы под каждое конкретное событие и я вполне мог использовать
массив объектов вместо массива данных. То есть создать отдельный класс-коллекцию событий, который и будет передан в конструктор.
У самих объектов есть метод `prepare()`, который как будто бы не передаёт смысловую нагрузку. Думаю, стоит его переименовать
в `toArray()` для наглядности.

### Пример 3.2
А завершить хочу ещё одним примером, который относится к "Проблемам CMS". Есть у нас объект `Cart`, который содержит в себе
массив `Item`. Но получить сразу объекты из корзины я не могу. Только в виде массива. Что создаёт дополнительные проблемы
и вынуждают приводить всё к нужным типам на уровне моего плагина. Правда, никто меня не защищает от того, что в исходном
объекте придут совершенно-неправильные данные, которые в данный момент не обрабатываются моими методами. Вот это точно
надо поправить.


## Выводы

### Пример 1.1
Добавил методы для проверки существования пользователей по тем данным, которые переданы в тот или иной хук. Остальное
должно отсекаться строгой типизацией в методах класса.

### Пример 1.2
В качестве решения указанной проблемы, я создал абстрактный класс `EventParams`, у которого есть наследник для каждого из
типов событий. Соответственно, интерфейс класса `Event` теперь принимает на вход объект `EventParams`, а не массив данных.
Что уменьшило вероятность ошибок при создании событий.

### Пример 2.1
Здесь всё достаточно просто. Выносим определение `ID` пользователя из класса во внешнюю систему. К тому же, CMS в данном
случае нам уже предоставляет функцию для получения `ID` пользователя.

### Пример 2.2
Интересно, что классы `User` и `Session` я определяю исключительно в одном месте. Это singleton-класс для аналитики. 
Соответственно, похоже, что мне на уровне этого класса нужно знать и генерировать `ID` сессии. А обрабатывать сессию я
буду уже в других классах плагина. Так как есть кейсы, когда `ID` может быть необходимо переопределить. Соответственно,
сам класс получает `ID` сессии из внешней системы, а внутри предоставляет интерфейс для того, чтобы получить или обновить
его.

### Пример 3.1
Добавил класс `EventCollection`, который собирает в себе массив из объектов `Event`. Также переименовал метод `prepare()`.
Класс с учётом всех изменений в соответствующей папке. От таких изменений получаю явное эстетическое удовольствие.

### Пример 3.2
Добавил валидацию данных item-а, чем защитил себя от неправильных данных при получении из корзины. Но наличие CMS во всей
этой истории нравится всё меньше и меньше.