abstract class NativaDictionary<T>
{
    // Конструктор
    public NativaDictionary<T> NativaDictionary(int size); // постусловие: создан новый пустой словарь размера size

    // команды:
    public void put(T key, T value); // постусловие: в словарь добавлена новая пара ключ-значение

    // предусловие: ключ присутствует в словаре
    public void remove(T key); // постусловие: из словаря удалена пара ключ-значение

    // запросы:
    // предусловие: словарь не пустой
    public T get(T key); // возвращает значение по ключу

    public int size(); // постусловие: размер словаря возвращён

    public bool containsKey(T key); // содержится ли ключ в словаре

    // запросы статусов (возможные значения статусов)
    public int get_put_status(); // успешно; элемент заменён
    public int get_remove_status(); // успешно; элемент не найден
    public int get_get_status(); // успешно; элемент не найден
}