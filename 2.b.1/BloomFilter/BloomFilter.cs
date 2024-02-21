abstract class BloomFilter
{
    // Конструктор
    public BloomFilter(int filter_len); // постусловие: создан новый фильтр Блума размера filter_len

    // команды:
    public void add(string str); // постусловие: в фильтр добавлена строка

    // запросы:
    public bool is_value(string str); // возвращает true, если строка имеется в фильтре,
                                      // иначе false

    // запросы статусов (возможные значения статусов)
    public int get_status(); // успешно; элемент уже существует
}