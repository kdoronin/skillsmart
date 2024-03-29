abstract class Queue<T>
{
    // Конструктор
    public Queue<T> Queue(); // постусловие: создана новая пустая очередь

    // команды:
    public void enqueue(T value); // постусловие: в конец очереди добавлен новый элемент

    // предусловие: очередь не пустая
    public T dequeue(); // постусловие: из очереди удалён и возвращён элемент

    // запросы:
    public int size(); // постусловие: размер очереди возвращён

    // предусловие: очередь не пустая
    public T get(); // постусловие: первый элемент очереди возвращён

    // запросы статусов (возможные значения статусов)
    public int get_dequeue_status(); // успешно; очередь пустая
    public int get_get_status(); // успешно; очередь пустая
}