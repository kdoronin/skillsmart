abstract class ParentQueue<T>
{
        // Конструктор
    public Queue<T> Queue(); // постусловие: создана новая пустая очередь

    // команды:
    public void add_tail(T value); // постусловие: в конец очереди добавлен новый элемент

    // предусловие: очередь не пустая
    public T remove_head(); // постусловие: из очереди удалён и возвращён элемент

    // запросы:
    public int size(); // постусловие: размер очереди возвращён

    // предусловие: очередь не пустая
    public T get_head(); // постусловие: первый элемент очереди возвращён

    // запросы статусов (возможные значения статусов)
    public int get_add_tail_status(); // успешно; очередь пустая
    public int get_get_head_status(); // успешно; очередь пустая
}

abstract class Queue<T>: ParentQueue
{
    // Конструктор
    public Queue<T> Queue(); // постусловие: создана новая пустая очередь
}

abstract class Deque<T>: ParentQueue
{
    // Конструктор
    public Deque<T> Deque(); // постусловие: создана новая пустая очередь

    // команды:
    public void add_head(T value); // постусловие: в начало очереди добавлен новый элемент

    // предусловие: очередь не пустая
    public T remove_tail(); // постусловие: из очереди удалён и возвращён элемент

    // запросы:
    public T get_tail(); // постусловие: последний элемент очереди возвращён

    // запросы статусов (возможные значения статусов)
    public int get_add_head_status(); // успешно; очередь пустая
    public int get_get_tail_status(); // успешно; очередь пустая
}