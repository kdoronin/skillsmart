<?php

/**
 * Исходная ЦС метода: 22
 * Целевая ЦС метода: 11
 * Итоговая ЦС метода: 8
 *
 * Анализ ситуации до:
 * 1. Больше всего на увеличение ЦС в методе влияет switch-case, внутри которого используются ещё и дополнительные
 *    условия
 * 2. Сама по себе логика валидации достаточно запутанная и зависит не только от бизнес-логики, но и от формата данных,
 *    получаемый от другого сервиса
 * 3. Гипотеза в том, что если вынести switch-case в отдельную абстракцию, то это добавит модулю больше понятности при
 *    взаимодействии и обеспечит более естественное развитие модуля.
 *
 * Результат работы:
 * 1. Вынес часть процесса валидации в отдельный класс-валидатор
 * 2. Добавил отдельную сущность для правил валидации с универсальным интерфейсом
 * 3. Валидатор знает, какие классы отвечают за валидацию какого параметра scheduleType
 * 4. В итоге, с применением новых абстракций, ЦС целевого метода снизился до 8
 *
 * Вывод:
 * Скорее всего, сам метод стоило бы полностью отрефакторить и переработать структуру всего модуля. Но в рамках текущей
 * задачи остановился на снижении ЦС
 */

defined('ABSPATH') || exit;

/**
 * Validation only for items events for add to card and checkout
 */
class EventValidator extends AbstractController
{
    protected static $_instance = null;

    private function isItemValidByWowordersEvent(array $item, array $event): bool
    {
        if (!$scheduleType = get_post_meta($item['variation_id'], '_schedule_type', true)) {
            return false;
        }

        $isAvailable = EventValidator::validate($scheduleType, $event);

        /**
         * Ignore standard availability for cfr items
         */
        if ($item['isCfr'] ?? false) {
            $isAvailable = $event['isCfr'] && $event['isAvailable'] && !$event['isLocked'];
        }

        /**
         * Select parent booster id if it exists
         */
        $eventBoosterId = $event['booster']['parent']['id'] ?? $event['booster']['id'];
        $isBoosterValid = (int)($eventBoosterId) === (int)$item['booster_id'];
        return $isAvailable && $isBoosterValid;
    }
}


class EventValidator {
    private string $eventType = '';
    private array $event = [];

    private static array $scheduleTypes = [
        'simple' => MLRule::class,
        'advanced' => PLRule::class,
        'premium' => PLRule::class,
        'sherpa' => SherpaRule::class,
        'recovery' => RecoveryRule::class
    ];

    public static function validate(string $scheduleType, array $event): bool
    {
        if (!isset(self::$scheduleTypes[$scheduleType])) {
            return false;
        }
        return self::$scheduleTypes[$scheduleType]::validate($event);
    }
}

interface EventRule {
    public static function validate(array $event): bool;
}

class MLRule implements EventRule {
    public static function validate(array $event): bool
    {
        return $event['isAvailable'] && !$event['isLocked'];
    }
}

class SherpaRule implements EventRule {
    public static function validate(array $event): bool
    {
        return $event['splLimitWTransfer']['isAvailable'] && !$event['isLocked'];
    }
}

class RecoveryRule implements EventRule {
    public static function validate(array $event): bool
    {
        return $event['splLimitTransfer']['isAvailable'] && !$event['isLocked'];
    }
}

class PLRule implements EventRule {
    public static function validate(array $event): bool
    {
        return PLRule::hasArmorType($event) && PLRule::isArmorTypeAvailable($event);
    }

    private function hasArmorType($event): bool
    {
        return is_null($event['selectedArmorType'] ?? null);
    }

    private function isArmorTypeAvailable($event): bool
    {
        foreach ($event['wowArmors'] as $armor) {
            if ($armor['type'] === $event['selectedArmorType']) {
                return $armor['isAvailable'] && !$event['isLocked'];
            }
        }
        return false;
    }
}