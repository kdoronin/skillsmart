<?php

use Theme\Modules\Fetcher;
use Theme\AbstractController;

class EventValidator extends AbstractController
{
    protected static $_instance = null;

    /**
     * Set handlers for add to cart and checkout filters
     */
    protected function onInit(): void
    {
        add_filter('add_to_cart_validation', [$this, 'isItemWithEventValid'], 10, 1);
        add_action('woocommerce_checkout_process', [$this, 'isCheckoutItemWithEventValid']);
    }

    /**
     * Validation only for checkout, when client trying to order products
     * Remove items with unavailable events
     */
    public function isCheckoutItemWithEventValid(): void
    {
        global $woocommerce;
        $cart = $woocommerce->cart;
        $items = $cart->get_cart();
        foreach ($items as $itemId => $item) {
            $itemData = $this->getCheckoutItemDataForValidation($item);

            if (!$this->isItemWithEventValid($itemData, $itemId)) {
                $cart->remove_cart_item($itemId);
                wc_add_notice(__('Sorry, the selected time slot is no longer available. Please choose another time on product page.', 'lang'), 'error');
            }
        }
    }

    /**
     * Select cart item data for validation
     *
     * @param array $item
     * @return array
     */
    private function getCheckoutItemDataForValidation(array $item): array
    {
        return [
            'schedule-id' => $item['schedule-id'] ?? ($item['shedule-id'] ?? null),
            'schedule-time' => $item['schedule-time'] ?? null,
            'event_type_id' => $item['event_type_id'] ?? null,
            'product_id' => $item['product_id'] ?? null,
            'variation_id' => $item['variation_id'] ?? null,
            'booster_id' => $item['booster_id'] ?? null,
            'spec-name' => $item['spec-name'] ?? null,
            'attributes' => isset($item['data']) ? $item['data']->get_variation_attributes(true) : null,
            'isCfr' => $this->isCfr($item),
        ];
    }

    /**
     * If item with schedule - validate it by time and event
     * Try to update item time if item id is not empty and item event time has passed
     *
     * @param array $item
     * @param string $itemId
     * @return bool
     */
    public function isItemWithEventValid(array $item, string $itemId = ''): bool
    {
        if (!$this->isItemWithSchedule($item)) {
            return true;
        }

        $events = $this->fetchEvents($item);
        $event = $this->getItemEvent($item, $events);
        if (!$event) {
            return false;
        }
        $isTimeValid = $this->isItemValidByTime($item, $event);
        if (!$itemId && !$isTimeValid) {
            return false;
        } elseif ($itemId && !$isTimeValid) {
            $this->updateItemEventTime($event, $itemId);
        }
        if (!$this->isItemValidByEvent($item, $event)) {
            return false;
        }
        return true;
    }

    /**
     * Select event by its id or return null
     *
     * @param array $item
     * @param array $events
     * @return array|null
     */
    private function getItemEvent(array $item, array $events): ?array
    {
        $eventId = $item['schedule-id'] ?? ($item['shedule-id'] ?? null);
        foreach ($events as $event) {
            if ((int)$event['id'] === (int)$eventId) {
                return $event;
            }
        }
        return null;
    }

    /**
     * Update cart item data when event time was extended
     *
     * @param array $event
     * @param string $itemId
     * @return void
     */
    private function updateItemEventTime(array $event, string $itemId): void
    {
        if (!$ts = $event['datetime'] ?? false) {
            return;
        }
        $cartItem = WC()->cart->cart_contents[$itemId];
        $dtz = new \DateTimeZone(($cartItem['pa_region'] ?? false) === 'us' ? 'America/New_York' : 'Europe/Paris');
        $dt = new \DateTime();
        $dt->setTimezone($dtz);
        $dt->setTimestamp($ts);
        $cartItem['schedule-time'] = $ts;
        $cartItem['raid-time'] = $dt->format('D j M \@ H:i T');
        WC()->cart->cart_contents[$itemId] = $cartItem;
        WC()->cart->set_session();
    }

    /**
     * Validate item by its schedule time and by event time
     *
     * @param array $item
     * @param array $event
     * @return bool
     */
    private function isItemValidByTime(array $item, array $event): bool
    {
        /**
         * Items without schedule-time is available by time, admin dashboad checkbox feature
         */
        if (!isset($item['schedule-time'])) {
            return true;
        }
        return $item['schedule-time'] >= gmdate('U') && (int)$item['schedule-time'] === (int)$event['datetime'];
    }

    /**
     * Select event availability from  response
     *
     * @param array $item
     * @param array $events
     * @return bool
     */
    private function isItemValidByEvent(array $item, array $event): bool
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

    /**
     * Check item checkboxes and return true if one of them is cfr
     *
     * @param array $item
     * @return bool
     */
    private function isCfr(array $item): bool
    {
        $cfrCheckboxes = get_option('wv_iscfr_checkboxes', []);
        $itemCheckboxes = array_keys($item['checkboxes'] ?? []);
        if (empty($cfrCheckboxes) || empty($itemCheckboxes)) {
            return false;
        }

        foreach ($itemCheckboxes as $slug) {
            $id = wc_attribute_taxonomy_id_by_name($slug);
            if ($cfrCheckboxes[$id] ?? false) {
                return true;
            }
        }

        return false;
    }

    /**
     * The item has schedule when it is with schedule or shedule (typo in project) id parameter
     *
     * @param array $item
     * @return bool
     */
    private function isItemWithSchedule(array $item): bool
    {
        return isset($item['schedule-id']) || isset($item['shedule-id']);
    }

    /**
     * Get events for item
     *
     * @param array $item
     * @return array
     */
    private function fetchEvents(array $item): array
    {
        $query = $this->getQuery($item);
        $fetcher = new Fetcher();
        $events = $fetcher->request($query);
        return $events;
    }

    /**
     * Get query for fetching events
     * Select endpoint events/wp when spec-name exists and pa_loot-system is not simple
     * Select only future events because of request parameter isPast has default value false
     *
     * @param array $item
     * @return string
     */
    private function getQuery(array $item)
    {
        $params = array_filter([
            'isCfr' => $item['isCfr'] ? 'true' : null,
            'eventType' => $item['event_type_id'],
            'id' => $item['schedule-id'] ?? $item['shedule-id'],
        ]);
        $attrs = $this->getItemAttributes($item);
        return 'events/wp?' . http_build_query(array_merge($params, $attrs));
    }

    /**
     * Get only needed item attrs data in required format for building  query
     *
     * @param array $item
     * @return array
     */
    private function getItemAttributes(array $item): array
    {
        if (isset($item['attributes'])) {
            $itemData = $item['attributes'];
        } else {
            $itemData = $item;
        }
        unset(
            $itemData['pa_region'],
            $itemData['event_region_slug'],
            $itemData['pa_choose-your-region'],
            $itemData['attribute_pa_choose-your-region'],
            $itemData['attribute_pa_game'],
        );

        $attrs = [];
        foreach ($itemData as $key => $value) {
            if (explode('_', $key)[0] === 'attribute' && !empty($value)) {
                $attrs[$key] = $value;
            }
        }
        if (array_key_exists('attribute_pa_loot-system', $attrs)) {
            if (strpos($attrs['attribute_pa_loot-system'], 'standard') !== false) {
                $attrs['pa_loot-system'] = 'simple';
            } elseif (strpos($attrs['attribute_pa_loot-system'], 'advanced') !== false) {
                $attrs['pa_loot-system'] = 'advanced';
            } elseif (strpos($attrs['attribute_pa_loot-system'], 'premium') !== false) {
                $attrs['pa_loot-system'] = 'premium';
            }
            unset($attrs['attribute_pa_loot-system']);
        }

        if (isset($item['spec-name'])) {
            $attrs['spec-name'] = $item['spec-name'];
        } elseif (isset($item['attribute_pa_spec-name'])) {
            $attrs['spec-name'] = $item['attribute_pa_spec-name'];
        }

        return $attrs;
    }
}


class Fetcher
{
    public function request(string $queryParams, array $args = []): array
    {
        global $business_logger;
        $result = [];
        try {
            $requestParams = $this->getRequestParams($queryParams, $args);
            $response = wp_remote_request($requestParams['url'], $requestParams['args']);
            if (wp_remote_retrieve_response_code($response) !== 200) {
                $errorMessage = is_wp_error($response) ?
                    $response->get_error_message() :
                    wp_remote_retrieve_response_code($response) . ' ; ' . wp_remote_retrieve_response_message($response);

                throw new Exception('[][request]  request: ' . $requestParams['url'] . '[][response]  response: ' . $errorMessage);
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            $result = $body['data'] ?? [];
        } catch (Exception $exception) {
            $business_logger->info($exception->getMessage());
        }

        return $result;
    }

    private function getRequestParams(string $queryParams, array $args): array
    {
        $Url = _URL;
        $AuthToken = _TOKEN;
        if (!$AuthToken) {
            throw new Exception('[][authentication]  authentication token unsetted.');
        }
        if (!$Url) {
            throw new Exception('[][authentication]  url unsetted.');
        }
        return [
            'url' => $Url . '/' . $queryParams,
            'args' => [
                'method' => 'GET',
                'headers' => [
                    'X-Auth-Token' => $AuthToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                ...$args,
            ]
        ];
    }
}