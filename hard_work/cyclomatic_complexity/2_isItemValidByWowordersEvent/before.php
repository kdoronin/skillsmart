<?php


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

        switch ($scheduleType) {
            case 'simple':
                $isAvailable = $event['isAvailable'] && !$event['isLocked'];
                break;
            case 'advanced':
            case 'premium':
                $isAvailable = false;
                if (is_null($selectedArmor = $event['selectedArmorType'] ?? null)) {
                    break;
                }
                foreach ($event['wowArmors'] as $armor) {
                    if ($armor['type'] === $selectedArmor) {
                        $isAvailable = $armor['isAvailable'] && !$event['isLocked'];
                        break;
                    }
                }
                break;
            case 'sherpa':
                $isAvailable = $event['splLimitWTransfer']['isAvailable'] && !$event['isLocked'];
                break;
            case 'recovery':
                $isAvailable = $event['splLimitTransfer']['isAvailable'] && !$event['isLocked'];
                break;
            default:
                $isAvailable = false;
        }

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