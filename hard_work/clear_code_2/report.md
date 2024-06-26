## 2. Уровень классов

### 2.1
В одном из предыдущих занятий по Hard Work я уже приводил в качестве примера класс из коммерческого плагина, 
который называется `PEWC_Product_Extra_Post_Type`. В классе 995 строк. Как будто бы туда хотели добавить вообще всё, что
может иметь отношение к данному типу записей. При этом в классе явным образом отделяется всё, что относится к обработке
мета-полей. Более того – в классе `PEWC_Product_Extra_Post_Type` есть даже вывод в HTML, который стоило вынести в отдельный
класс-шаблонизатор (или хотя бы вынести сам HTML в отдельные файлы, подключаемые извне).

По поводу второй части задания. У класса в программе будет много инстансов, в частности, если он изначально спроектирован,
как "швейцарский нож". Поэтому в программе в разных местах могут использоваться только какие-то его части. Это, к слову,
позволяет явным образом понять, где на самом деле должен заканчиваться один класс и начинаться другой.

### 2.2
В одном из тестовых встретился мне класс `SVG_Obj`. Он в себе содержал ровно один метод – `get_svg_file`, который отвечал за 
получение и отображение svg-файла. Мало того, что метод не отвечал принципу SRP, так ещё и в самом классе был список параметров, 
в котором был ассоциативный массив из 2-х элементов, соотносящий ключ и путь к файлу.

По-хорошему в этот класс нужно добавить все методы для работы с svg. И расширять уже существующий `WP_Image_Editor` путём
добавления поддержки svg-формата.

### 2.3
Существует класс `Fields` в нашем проекте. В котором я отыскал метод `changeFieldPriceStep`. Который, очевидно, должен быть
внутри класса `Field`, так как относится к одному классу. А большой класс `Fields` вообще отвечает за интеграцию с полями и 
его бы следовало назвать `FieldsCustomizer`.

### 2.4
В одном из плагинов у меня есть класс `AnalyticsSingleton`. И у него задача такая – собирать данные обо всех инициализированных
в плагине событиях, а потом разом их отправлять в Google Analytics. Я считаю это достаточно элегантным решением, так как
оно позволяет уменьшить количество обращений на бэкенде к внешнему сервису.

### 2.5
Конкретный пример сейчас найти не могу, но несколько раз встречал историю, когда в одном классе вызываются проверки из другого класса
или даже из нескольких. В итоге класс, к которому обращаемся, передаёт лишнюю функциональность вовне. И вместо того, чтобы
сохранять метод проверки внутри реализации конкретной задачи, он даёт доступ к нему извне.

Решением в данном случае является применение техники из ООАП-1 и оставить внешним классам доступ только к интерфейсу, скрыв
реализацию внутри исходного класса.

### 2.6
Конкретного примера с таким подходом под рукой не нашёл. Применять такое могут, когда нужно использовать метод, существующий
только в дочернем классе. Приведение к дочернему типу создаёт проблемы, так как может быть использовано позднее связывание
и родительский класс выше по программе уже был связан с другим дочерним классом. Что и вызовет ошибку при выполнении.

### 2.7
Похоже, что это история с ошибочным применением "Абстрактной фабрики". Когда разработчик путает характеристику и сущность.
В абстрактной фабрики есть продукты, у которых могут быть характеристики. По одной на класс. Но если мы добавим, например,
две характеристики, то при добавлении каждого нового класса с этой характеристикой, нам придётся создавать и другие родственные
классы, чтобы также поддерживать эту характеристику.

### 2.8
Как правило, такое происходит из-за ошибок при проектировании системы классов. 
Если требуется переопределение метода в дочернем классе, то лучше использовать вместо наследования композицию. Так мы уменьшаем связность. 
А в случае с не используемыми методами и атрибутами, можно сделать вывод, что родительский класс слишком обобщённый и стоит
либо разбить его на несколько более конкретных, либо уменьшить его уровень абстракции.

## 3. Уровень приложения

### 3.1
Могу привести сразу пачку таких проблем, которые есть в проекте. Например, у нас долгое время существовало две версии классов
для взаимодействия с внешним API. И каждый раз, когда внешняя система производила у себя какие-то изменения, мы ходили в оба
класса и правили там код. К счастью, сейчас удалось это легаси вычистить.
Второй пример – использование "магических констант". И некоторые из них использовались в нескольких частях системы.
Самое неприятное здесь – это то, что сами эти константы существовали также в базе данных. И у нас было правило, что в базе 
данных их менять никак нельзя.

Все проблемы такого плана вызваны высокой связностью системы. Разбиваем её на части, делаем их более независимыми друг от
друга и придёт счастье.

### 3.2
И вновь возвращаемся к ООАП-1. Или к TDD. В общем-то к любой системе, где есть фаза рефлексии о том, что у нас получилось
и как это упростить. При этом оба подхода позволяют выстраивать систему от малого к большому. А не сделать композицию 
из нескольких паттернов ООП, не вникая в их суть. Тут, собственно, вспоминается материал про паттерн Visitor, который
единственный остаётся, если мы начинаем следовать концепции функционального программирования.