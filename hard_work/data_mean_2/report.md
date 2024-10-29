# Отчёт

## Пример №1
### Было
```php
public function transform(array $events, Product $product): array
{
    $models = [];
    foreach ($events as $event) {
        $slotsData = $this->getEventsSlotsDataByLootSystem($event, $product);
        $models[] = new Event([
            'eventId' => (int)$event['id'],
            'isAvailable' => (bool)$slotsData['isAvailable'],
            'slotsLeft' => (int)$slotsData['slotsLeft'],
            'dateAndTime' => (int)$event['datetime'],
        ]);
    }
    return $models;
}
```
### Стало
```php
public function transform(array $events, Product $product): array 
{
    return array_map(function($event) use ($product) {
        $slotsData = $this->getEventsSlotsDataByLootSystem($event, $product);
        return new Event([
            'eventId' => (int)$event['id'],
            'isAvailable' => (bool)$slotsData['isAvailable'],
            'slotsLeft' => (int)$slotsData['slotsLeft'],
            'dateAndTime' => (int)$event['datetime'],
        ]);
    }, $events);
}
```

### Комментарий
Вместо создания дополнительной мутабельной переменной используется иммутабельный функциональный подход через `array_map`. Что позволило уменьшить количество переменных в методе в целом.

## Пример №2

### Было
```php
class Event extends ModelSetter
{
    public int $eventId;
    public bool $isAvailable;
    public int $slotsLeft;
    public int $dateAndTime;

    public function __construct(array $params)
    {
        foreach ($params as $key => $param) {
            self::__set($key, $param);
        }
    }
}
```
### Стало
```php 
class Event
{
    private function __construct(
        private readonly int $eventId,
        private readonly bool $isAvailable,
        private readonly int $slotsLeft,
        private readonly int $dateAndTime
    ) {}
    
    public static function fromArray(array $data): self 
    {
        return new self(
            eventId: (int)$data['eventId'],
            isAvailable: (bool)$data['isAvailable'],
            slotsLeft: (int)$data['slotsLeft'],
            dateAndTime: (int)$data['dateAndTime']
        );
    }
    
    public function withUpdatedSlots(int $newSlotsLeft): self
    {
        return new self(
            $this->eventId,
            $this->isAvailable,
            $newSlotsLeft,
            $this->dateAndTime
        );
    }
}
```
### Комментарий
В данном примере бизнес-логика не подразумевает изменение событий со стороны приложения. Все события, которые мы можем обработать в рамках нашей системе, мы не можем менять. Соответственно, выглядит очень логично превратить класс `Event` в иммутабельный класс, задача которого только в том, чтобы передать информацию к другим частям программы.

## Пример №3
###  Было:

```php
class Request 
{
    private static self $instance;
    public int $id;
    public string $region;
    public array $attributes = [];
    public bool $freeRun = false;
    
    public static function getInstance(array $request = []): self
    {
        if (!isset(self::$instance) || !empty($request)) {
            self::$instance = new self($request);
        }
        return self::$instance;
    }
}
```

### Стало:
```php
class RequestData
{
    private readonly int $id;
    private readonly string $region;
    private readonly array $attributes;
    private readonly bool $freeRun;
    
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->region = $data['region'];
        $this->attributes = $data['attributes'] ?? [];
        $this->freeRun = $data['freeRun'] ?? false;
    }
    
    public function getId(): int { return $this->id; }
    public function getRegion(): string { return $this->region; }
    public function getAttributes(): array { return $this->attributes; }
    public function isFreeRun(): bool { return $this->freeRun; }
}
```

### Комментарий
Из синглтона вынес отдельный иммутабельный класс со всеми параметрами, которые формируют один запрос.
Что позволит лучше отделять один запрос от другого и в целом обеспечит больше контроля над данными.