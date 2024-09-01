# Отчёт

## Пример №1

### Было
```php

if ($this->isDataForAddToCart($cart, $passed, $_POST) && $attribute_check && $this->validateCheckboxes($_POST['variation_id'], $cart_item_data)) {
     if ($this->isItemForSingleAdding($_POST, $cart)) {
         $cart_item_data['unique_key'] = $cart->generate_cart_id($_POST['product_id'], $_POST['quantity'], $_POST['variation_id'], array_merge($variation, ['uniqid' => uniqid()]));
     }
$cart_item_key = $cart->add_to_cart($_POST['product_id'], $_POST['quantity'], $_POST['variation_id'], $variation, $cart_item_data);
```
### Стало
```php
$cartItem = new CartItem($_POST['product_id'], $_POST['quantity'], $_POST['variation_id'], $variation, $cart_item_data);
$cart_item_key = $cart->addValidatedItem($cartItem);
```

### Описание
Создал отдельный класс для элемента корзины. Что позволило инкапсулировать все проверки между этим новым классом и методом
`addValidatedItem` в классе `Cart`.

## Пример №2
Здесь существует массив строк с возможными регионами. И везде, где идёт проверка на регионы, он присутствует в том или ином 
виде. Вызывая, в том числе, множественные вложенные условия. 

### Было
```php
    public static function getSanitizedRegion(string $region = ''): string
    {
        $region = strtolower($region);
        if (in_array($region, ['eu', 'us'])) {
            return $region;
        } else {
            $uc = wowvendor_get_theme()->getModule('user_country');
            if (is_object($uc)) {
                return $uc->getRegion();
            } else {
                return 'us';
            }
        }
    }

```

### Стало
```php
    public static function getSanitizedRegion(string $region = ''): string
    {
        return (new Region($region))->getValue();
    }
```

### Вывод
Я завёл для сущности регион отдельный класс `Region`, который на этапе создания сразу валидирует переданное значение.
Таким образом на уровне дизайна мы не поддерживаем передачу из API невалидного значения региона. При этом и сама проверка
перешла от частного случая к решению на уровне класса.

## Пример №3
Есть сущность "мультипликатор", зависящий от выбранной системы лута. И в классе для трансформации продукта присутствуют
методы, которые решают задачу вместо класса мультипликатора. Примеры 3 и 4 решаются выносом ArmorMultipier в качестве 
отдельного класса. 

### Было
```php
public function getArmorMultiplier(array $product): int
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
            if (str_contains($selectedAttributes['pa_loot-system'], 'standard-ext')) {
                $armorMultiplier = 6;
            }
            if ($selectedAttributes['pa_loot-system'] === 'deluxe10'
                || $selectedAttributes['pa_loot-system'] === 'deluxe20'
                || $selectedAttributes['pa_loot-system'] === 'deluxe26') {
                $armorMultiplier = 7;
            }
        }

        return $armorMultiplier;
    }
```

### Стало
```php
public function getArmorMultiplier(array $product): int
{
    $lootSystem = $product['attributesWithoutPrefix']['pa_loot-system'] ?? '';
    return ArmorMultiplier::get($lootSystem);
}
```

### Вывод
Теперь внутри класса `ArmorMultiplier` находится вся логика по определению правильного мультипликатора.
Что позволяет сильно упростить логику поведения не только класса-трансформера продуктов, но и других, использующих
мультипликаторы.

## Пример №4
Пример метода из того же класса, который был разрешён введением класса `ArmorMultiplier`. Вынес его отдельно, чтобы показать
пример условия типа

### Было
```php
private function isSelectedSystemNonStandard(array $product): bool
    {
        return isset($product['attributesWithoutPrefix']['pa_loot-system']) && (in_array(
                $product['attributesWithoutPrefix']['pa_loot-system'],
                ['advanced-pl', 'premium-pl', 'deluxe10', 'deluxe20', 'deluxe26']
            ) || str_contains($product['attributesWithoutPrefix']['pa_loot-system'], 'standard-ext'));
    }
```
### Стало
```php
private function isSelectedSystemNonStandard(array $product): bool
{
    $lootSystem = $product['attributesWithoutPrefix']['pa_loot-system'] ?? '';
    return ArmorMultiplier::isNonStandard($lootSystem);
}
```
При этом в классе `ArmorMultiplier` метод выглядит вот так:
```php
public static function isNonStandard(string $lootSystem): bool
    {
        return isset(self::MULTIPLIERS[$lootSystem]) || str_contains($lootSystem, 'standard-ext');
    }
```

### Вывод
Перенос условного оператора на другой уровень может сильно упростить код условий. 

## Пример №5
Это мини-пример из кода, который я писал по Object Calisthenics. Проблема у него в том, что я дважды вызываю метод.
Хотя этого можно избежать.

### Было
```php
    public function addToCart(string $cart_id, int $product_id, int $request_quantity, $variation_id, $variation, $cart_item_data): void
    {
        $event = EventFactory::create('add_to_cart', [
            'item_id' => $cart_id,
            'quantity' => $request_quantity,
            'cart' => WC()->cart,
        ]);
        if($event->prepare() !== []) {
            $this->analytics->addBackendEvent($event->prepare());
        }
    }

```

### Стало
```php
$preparedEvent = $event->prepare();
if (!empty($preparedEvent)) {
    $this->analytics->addBackendEvent($preparedEvent);
}
```

### Вывод
Таким образом простое изменение места вызова метода `prepare` позволило избежать дублирования.