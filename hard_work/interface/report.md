## Примеры призрачного состояния

### Пример №1

#### До
```php
private function getQuery(Product $product): string
    {
        $dataTimeCurrentUTC = Request::getInstance()->currentUtc;
        $freeRun            = Request::getInstance()->freeRun;
        $raidType           = Request::getInstance()->raidType;
        $dts                = date('Y-m-d H:i:s', $dataTimeCurrentUTC);
        $dte                = date('Y-m-d H:i:s', strtotime($this->getTimeIntervalIncrement($product), $dataTimeCurrentUTC));

        $args = [
            'game.externalId' => $product->gameId,
            'datetime'        => [
                'after'  => $dts,
                'before' => $dte,
            ],
            'region.name'     => $product->isWow ? $product->region : null,
            $this->getAttributesForFetchingProductsEvents($product),
            'eventType'       => $product->eventTypeId,
            'isCfr'           => $freeRun ? 'true' : null,
            'pageSize'        => 1500
        ];
        if ($raidType && $raidType !== '') {
            $args['raid_type'] = $raidType;
        }
        $query = add_query_arg(
            array_filter($args),
            $this->getEndpointName($product->attributesWithoutPrefix)
        );
        return $query;
    }
```


#### После
```php
private function getQuery(Product $product): string
    {
        $args = [
            'game.externalId' => $product->gameId,
            'datetime'        => [
                'after'  => $this->getDateTimeStart($product),
                'before' => $this->getDateTimeEnd($product),
            ],
            'region.name'     => $product->isWow ? $product->region : null,
            $this->getAttributesForFetchingProductsEvents($product),
            'eventType'       => $product->eventTypeId,
            'isCfr'           => Request::getInstance()->freeRun ? 'true' : null,
            'pageSize'        => 1500
        ];
        if (Request::getInstance()->raidType && Request::getInstance()->raidType !== '') {
            $args['raid_type'] = Request::getInstance()->raidType;
        }
        $query = add_query_arg(
            array_filter($args),
            $this->getEndpointName($product->attributesWithoutPrefix)
        );
        return $query;
    }
```

#### Комментарий
Убрал лишние переменные. Часть получаемых данных вынес в отдельные методы. Другие данные сразу получаю в нужный ключ массива.


### Пример №2

#### До
```php
public function transform(array $variation): Product
    {
        if ($variation['isWow']) {
            $variation['isSelectedSystemNonStandard'] = $this->isSelectedSystemNonStandard($variation);
            $variation['armorType']                   = $this->getArmorName($variation);
            $variation['armorMultiplier']             = $this->getArmorMultiplier($variation);
            // remove a spec name for a standard variation with the disabled spec selecting if the request was invalid
            if ( ! $variation['isSelectedSystemNonStandard'] && ! $variation['isSpecSelectingEnabled']) {
                unset(Request::getInstance()->attributes['spec-name']);
            }
        }

        return new Product(
            $variation + [
                'region'              => Request::getInstance()->region,
                'requestedAttributes' => Request::getInstance()->attributes
            ]
        );
    }
```

#### После
```php
public function transform(array $variation): Product
{
    $transformedData = $variation;

    if ($variation['isWow']) {
        $transformedData['isSelectedSystemNonStandard'] = $this->isSelectedSystemNonStandard($variation);
        $transformedData['armorType'] = $this->getArmorName($variation);
        $transformedData['armorMultiplier'] = $this->getArmorMultiplier($variation);

        $request = Request::getInstance();
        if (!$transformedData['isSelectedSystemNonStandard'] && !$variation['isSpecSelectingEnabled']) {
            $attributes = $request->attributes;
            unset($attributes['spec-name']);
            $request->setAttributes($attributes);
        }
    }

    $transformedData['region'] = Request::getInstance()->region;
    $transformedData['requestedAttributes'] = Request::getInstance()->attributes;

    return new Product($transformedData);
}
```
#### Комментарий
В данном случае, наоборот была добавлена ещё одна переменная. Которая не используется за пределами метода. Это позволит
избежать неожиданных изменений переданной на вход переменной `$variation`. Так как по сигнатуре метода он должен формировать
новый объект `Product`, а не изменять входные данные.



## Примеры, которые чрезмерно сужают логику кода

### Пример №1
#### До
```php
class TimeslotsFetcher
{
    private const FIFTEEN_DAYS = '+15 days';

    public function __construct()
    {
        $this->from = Request::getInstance()->currentUtc;
        $this->to = strtotime(self::FIFTEEN_DAYS, $this->from);
    }

    // ...
}
```

#### После
```php
class TimeslotsFetcher
{
    private const DEFAULT_TIME_RANGE = '+15 days';

    public function __construct(string $timeRange = self::DEFAULT_TIME_RANGE)
    {
        $this->from = Request::getInstance()->currentUtc;
        $this->to = strtotime($timeRange, $this->from);
    }

    // ...
}
```

#### Комментарий
Исходная спецификация класса позволяла выбирать события только за ближайшие 15 дней. Но потребность бизнесам может измениться
в любой момент. Поэтому я перенёс в конструктор фетчера возможность передавать нужный интервал времени. Оставив при этом
значение по-умолчанию на случай, если параметр не передан.


### Пример №2
#### До
```php
class EventFetcher
{
    private function getQuery(Product $product): string
    {
        $args = [
            // ...
            'pageSize' => 1500
        ];
        // ...
    }
    // ...
}
```

#### После
```php
class EventFetcher
{
    private const DEFAULT_PAGE_SIZE = 1500;

    private function getQuery(Product $product, int $pageSize = self::DEFAULT_PAGE_SIZE): string
    {
        $args = [
            // ...
            'pageSize' => $pageSize
        ];
        // ...
    }
    // ...
}
```

#### Комментарий
Ситуация, аналогичная предыдущему примеру. Количество событий в запросе может быть как больше, так и меньше, чем 1500.
Поэтому оставил это, как значение по-умолчанию, но добавил возможность передавать нужное количество событий в метод.


### Пример №3
#### До
```php
class ProductTransformer
{
    private function getArmorMultiplier(array $product): int
    {
        $armorMultiplier = 0;

        $selectedAttributes = $product['attributesWithoutPrefix'];
        if (isset($selectedAttributes['pa_loot-system'])) {
            if ($selectedAttributes['pa_loot-system'] === 'advanced-pl') {
                $armorMultiplier = 4;
            }
            if ($selectedAttributes['pa_loot-system'] === 'premium-pl') {
                $armorMultiplier = 5;
            }
            // ...
        }

        return $armorMultiplier;
    }
    // ...
}
```

#### После
```php
class ProductTransformer
{
    private const ARMOR_MULTIPLIERS = [
        'standard' => 1,
        'advanced-pl' => 4,
        'premium-pl' => 5,
        'standard-ext' => 6,
        'deluxe10' => 7,
        'deluxe20' => 7,
        'deluxe26' => 7,
    ];

    private function getArmorMultiplier(array $product): int
    {
        $selectedAttributes = $product['attributesWithoutPrefix'];
        $lootSystem = $selectedAttributes['pa_loot-system'] ?? 'standard';

        foreach (self::ARMOR_MULTIPLIERS as $system => $multiplier) {
            if (strpos($lootSystem, $system) !== false) {
                return $multiplier;
            }
        }

        return 1; // Default multiplier
    }
    // ...
}
```

#### Комментарий
В данном примере, вместо большого количества условий, я вынес все мультипликаторы в константу. И теперь, чтобы получить
то или иное значение, достаточно взять его из массива по ключу. Это делает функциональность более расширяемой и понятной.

## Примеры, когда интерфейс не должен быть проще реализации

В треке по Object Calisthenics мы рассматривали пример с автомобилем. Который, безусловно, содержит в себе целую массу 
деталей. Собственно, решение также предлагалось в виде создания отдельных классов для кузова и двигателя, например.
Думаю, через подобные механизмы можно работать и в других случаях.