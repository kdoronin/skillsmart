abstract class LinkedList<T>
{
    // константы
    public const int PUT_NIL = 0; // put_left() и put_right() ещё не вызывались
    public const int PUT_OK = 1; // последняя put_left() или put_right() отработала нормально
    public const int PUT_ERR = 2; // список пуст

    public const int REMOVE_OK = 1; // последняя remove() отработала нормально
    public const int REMOVE_ERR = 2; // список пуст

    public const int RIGHT_NIL = 0; // right() ещё не вызывалась
    public const int RIGHT_OK = 1; // последняя right() отработала нормально
    public const int RIGHT_ERR = 2; // текущий элемент - хвост списка (нет следующего элемента)
    public const int RIGHT_EMPTY = 3; // список пуст

    public const int FIND_OK = 1; // последняя find() отработала нормально
    public const int FIND_ERR = 2; // элемент не найден


    // конструктор
    public LinkedList<T> LinkedList(); // постусловие: создан новый пустой список

    // команды:
    // предусловие: список не пустой
    // постусловие: справа от текущего элемента добавлен новый элемент
    public void put_right(T value); 

    // предусловие: список не пустой
    // постусловие: слева от текущего элемента добавлен новый элемент
    public void put_left(T value); 

    // постусловие: в конец списка добавлен новый элемент
    public void add_tail(T value); 

    // постусловие: значение текущего узла заменено заданным
    public void replace(T value);

    // предусловие: список не пустой
    // постусловие: текущий элемент удалён
    public void remove(); 

    // постусловие: курсор установлен на следующий узел с искомым условием
    public void find(T value);

    // постусловие: все элементы с заданным значением удалены
    public void remove_all(T value);

    // предусловие: список не пустой
    // постусловие: текущим элементом становится голова списка
    public void head();

    // предусловие: список не пустой
    // постусловие: текущим элементом становится хвост списка
    public void tail();

    // предусловие: список не пустой и текущий элемент не является хвостом списка
    // постусловие: текущим элементом становится следующий элемент
    public void right();

    // постусловие: из списка удалятся все значения
    public void clear(); 

    // запросы:

    // предусловие: список не пустой
    public T get();

    public void size();
    
    public void is_head();

    public void is_tail();

    public void is_value();

      // запросы статусов (возможные значения статусов)
    public int get_head_status(); // успешно; список пуст
    public int get_tail_status(); // успешно; список пуст
    public int get_right_status(); // успешно; правее нету элемента
    public int get_put_right_status(); // успешно; список пуст
    public int get_put_left_status(); // успешно; список пуст
    public int get_remove_status(); // успешно; список пуст
    public int get_replace_status(); // успешно; список пуст
    public int get_find_status(); // следующий найден; 
                        // следующий не найден; список пуст
    public int get_get_status(); // успешно; список пуст
}

// 2.2. Из-за времени обхода всего списка
// 2.3. Решается через установку курсора на голову списка, а потом поиском элементов до тех пор, пока не дойдём до хвоста списка