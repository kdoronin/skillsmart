abstract class BoundedStack<T>

    // константы
    public const int POP_NIL = 0; // push() ещё не вызывалась
    public const int POP_OK = 1; // последняя pop() отработала нормально
    public const int POP_ERR = 2; // стек пуст

    public const int PEEK_NIL = 0; // push() ещё не вызывалась
    public const int PEEK_OK = 1; // последняя peek() вернула корректное значение
    public const int PEEK_ERR = 2; // стек пуст

    public const int PUSH_OK = 1; // последняя push() отработала нормально
    public const int PUSH_ERR = 2; // стек полон

    // конструктор
    public BoundedStack<T> BoundedStack(int max_size); // постусловие: создан новый пустой стек размера size


    // команды:
// предусловие: стек не полный;
// постусловие: в стек добавлено новое значение
    public void push(T value);

// предусловие: стек не пустой;
// постусловие: из стека удалён верхний элемент
    public void pop();

// постусловие: из стека удалятся все значения
    public void clear();


    // запросы:
// предусловие: стек не пустой
    public T peek();

    public int get_size();
    public int get_max_size();


    // дополнительные запросы:
    public int get_pop_status(); // возвращает значение POP_*
    public int get_peek_status(); // возвращает значение PEEK_*
    public int get_push_status(); // возвращает значение PUSH_*