abstract class PowerSet<T>: HashTable<T>
{
    // Конструктор
    public PowerSet<T> PowerSet(int sz); // постусловие: создано новое пустое множество размера sz

    public PowerSet<T> intersection(PowerSet<T> set2); // пересечение текущего множества и set2

    public PowerSet<T> union(PowerSet<T> set2); // объединение текущего множества и set2

    public PowerSet<T> difference(PowerSet<T> set2); // разница текущего множества и set2

    public bool isSubset(PowerSet<T> set2); // возвращает true, если set2 есть подмножество текущего множества, иначе false
}