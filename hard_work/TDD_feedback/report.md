## Плюсы, характерные для моего стиля применения TDD

1. Самый главный плюс – это покрытие тестами. Я очень дотошный, поэтому в голове у меня возникает огромное количество
ситуаций, которые могут возникнуть. Поэтому тестирую я всё, до чего додумываюсь
2. Определение интерфейсов и их удобства в применении. Бывало так, что я под гнётом тестов переопределял интерфейс и 
даже менял место разбиения на сущности.
3. Если сравнивать TDD с историей, когда "просто сел и начал писать", то он, определённо, позволяет реализовать более
качественный дизайн. Просто из-за того, что есть так необходимый этап рефлексии и он происходит регулярно.

## Минусы, характерные для моего стиля применения TDD
1. Из-за наличия в проекте фреймворков или даже CMS, возникает сложность с написанием тестов. Особенно когда внутри теста
используется функция из фреймворка. И не всегда удастся адекватно прописать все тесты, чтобы зависимость окончательно
оборвать.
2. Асинхронность. Она не поддаётся тестированию. А если и есть техники, то они уже не так "легки и просты", как описывает
TDD. 
3. Исходит из п.1 в плюсах. Какой-то тест всё равно может быть упущен. Хотя бы из-за волнообразности их появления. И, в идеале,
после окончания разработки стоит ещё раз проходить фаззингом, чтобы определять места, где тестов недостаточно.
4. Дизайн. Если его продумывать заранее в соответствии с PSP, он получается в разы лучше, чем то, что получается в итоге
в TDD. Да даже история с Object Calisthenics даёт куда лучший эффект. TDD в этом плане очень похож на эволюционное развитие
кода из ничего. И здесь может быть подвох, если программисту не хватает опыта, чтобы понять, какой из очередных "маленьких шагов"
хорош, а какой может привести к катастрофе.


## На что больше похож мой стиль
Я бы оставил за TDD роль "продуктивной техники тестирования". Всё-таки, по моему мнению, он не позволяет создавать действительно
хороший дизайн приложения.

