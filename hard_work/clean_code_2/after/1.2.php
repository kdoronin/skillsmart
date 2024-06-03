<?php


use Fetcher;
use AbstractController;

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
        $cart  = $woocommerce->cart;
        $items = $cart->get_cart();
        foreach ($items as $itemId => $item) {
            $itemData = $this->getCheckoutItemDataForValidation($item);

            if ( ! $this->isItemWithEventValid($itemData, $itemId)) {
                $cart->remove_cart_item($itemId);
                wc_add_notice(
                    __(
                        'Sorry, the selected time slot is no longer available. Please choose another time on product page.',
                        'lang'
                    ),
                    'error'
                );
            }
        }
    }

    /**
     * Select cart item data for validation
     *
     * @param array $item
     *
     * @return array
     */
    private function getCheckoutItemDataForValidation(array $item): array
    {
        return [
            'schedule-id'   => $item['schedule-id'] ?? ($item['shedule-id'] ?? null),
            'schedule-time' => $item['schedule-time'] ?? null,
            'event_type_id' => $item['event_type_id'] ?? null,
            'product_id'    => $item['product_id'] ?? null,
            'variation_id'  => $item['variation_id'] ?? null,
            'booster_id'    => $item['booster_id'] ?? null,
            'spec-name'     => $item['spec-name'] ?? null,
            'attributes'    => isset($item['data']) ? $this->getVariationAttributes($item['data']) : null,
            'isCfr'         => $this->isCfr($item),
        ];
    }

    /**
     * Get variation attributes
     *
     * @param WC_Product_Data $data
     *
     * @return array|null
     */
    private function getVariationAttributes($data): ?array
    {
        return $data->get_variation_attributes(true);
    }

    /**
     * If item with schedule - validate it by time and event
     * Try to update item time if item id is not empty and item event time has passed
     *
     * @param array $item
     * @param string $itemId
     *
     * @return bool
     */
    public function isItemWithEventValid(array $item, string $itemId = ''): bool
    {
        if ( ! $this->isItemWithSchedule($item)) {
            return true;
        }

        $events = $this->fetchEvents($item);
        $event  = $this->getItemEvent($item, $events);
        if ( ! $event) {
            return false;
        }
        $isTimeValid = $this->isItemValidByTime($item, $event);
        if ( ! $itemId && ! $isTimeValid) {
            return false;
        } elseif ($itemId && ! $isTimeValid) {
            $this->updateItemEventTime($event, $itemId);
        }
        if ( ! $this->isItemValidByEvent($item, $event)) {
            return false;
        }

        return true;
    }

    /**
     * Select event by its id or return null
     *
     * @param array $item
     * @param array $events
     *
     * @return array|null
     */
    private function getItemEvent(array $item, array $events): ?array
    {
        $eventId = $this->getEventId($item);
        foreach ($events as $event) {
            if ((int)$event['id'] === (int)$eventId) {
                return $event;
            }
        }

        return null;
    }

    /**
     * Get event ID from item
     *
     * @param array $item
     *
     * @return int|null
     */
    private function getEventId(array $item): ?int
    {
        return $item['schedule-id'] ?? ($item['shedule-id'] ?? null);
    }

    /**
     * Update cart item data when event time was extended
     *
     * @param array $event
     * @param string $itemId
     *
     * @return void
     */
    private function updateItemEventTime(array $event, string $itemId): void
    {
        if ( ! $ts = $event['datetime'] ?? false) {
            return;
        }
        $cartItem = $this->getCartItem($itemId);
        $dtz      = $this->getDateTimeZone($cartItem);
        $dt       = $this->getDateTime($dtz, $ts);
        $this->updateCartItem($itemId, $cartItem, $dt, $ts);
    }

    /**
     * Get cart item by item ID
     *
     * @param string $itemId
     *
     * @return array
     */
    private function getCartItem(string $itemId): array
    {
        return WC()->cart->cart_contents[$itemId];
    }

    /**
     * Get DateTimeZone object based on region
     *
     * @param array $cartItem
     *
     * @return \DateTimeZone
     */
    private function getDateTimeZone(array $cartItem): \DateTimeZone
    {
        $region   = $cartItem['pa_region'] ?? false;
        $timezone = ($region === 'us') ? 'America/New_York' : 'Europe/Paris';

        return new \DateTimeZone($timezone);
    }

    /**
     * Get DateTime object set to the specified timestamp and timezone
     *
     * @param \DateTimeZone $dtz
     * @param int $ts
     *
     * @return \DateTime
     */
    private function getDateTime(\DateTimeZone $dtz, int $ts): \DateTime
    {
        $dt = new \DateTime();
        $dt->setTimezone($dtz);
        $dt->setTimestamp($ts);

        return $dt;
    }

    /**
     * Update the cart item with new schedule time and formatted raid time
     *
     * @param string $itemId
     * @param array $cartItem
     * @param \DateTime $dt
     * @param int $ts
     *
     * @return void
     */
    private function updateCartItem(string $itemId, array $cartItem, \DateTime $dt, int $ts): void
    {
        $cartItem['schedule-time']         = $ts;
        $cartItem['raid-time']             = $dt->format('D j M \@ H:i T');
        WC()->cart->cart_contents[$itemId] = $cartItem;
        WC()->cart->set_session();
    }

    /**
     * Validate item by its schedule time and by event time
     *
     * @param array $item
     * @param array $event
     *
     * @return bool
     */
    private function isItemValidByTime(array $item, array $event): bool
    {
        /**
         * Items without schedule-time is available by time, admin dashboad checkbox feature
         */
        if ( ! isset($item['schedule-time'])) {
            return true;
        }

        return $item['schedule-time'] >= gmdate('U') && (int)$item['schedule-time'] === (int)$event['datetime'];
    }

    /**
     * Select event availability from response
     *
     * @param array $item
     * @param array $events
     *
     * @return bool
     */
    private function isItemValidByEvent(array $item, array $event): bool
    {
        if ( ! $scheduleType = $this->getScheduleType($item)) {
            return false;
        }

        $isAvailable = $this->checkEventAvailability($scheduleType, $event);

        /**
         * Ignore standard availability for cfr items
         */
        if ($this->isCfr($item)) {
            $isAvailable = $event['isCfr'] && $event['isAvailable'] && ! $event['isLocked'];
        }

        /**
         * Select parent booster id if it exists
         */
        $eventBoosterId = $this->getEventBoosterId($event);
        $isBoosterValid = (int)($eventBoosterId) === (int)$item['booster_id'];

        return $isAvailable && $isBoosterValid;
    }

    /**
     * Get schedule type from item
     *
     * @param array $item
     *
     * @return string|null
     */
    private function getScheduleType(array $item): ?string
    {
        return get_post_meta($item['variation_id'], '_schedule_type', true);
    }

    /**
     * Check event availability based on schedule type
     *
     * @param string $scheduleType
     * @param array $event
     *
     * @return bool
     */
    private function checkEventAvailability(string $scheduleType, array $event): bool
    {
        switch ($scheduleType) {
            case 'simple':
                return $event['isAvailable'] && ! $event['isLocked'];
            case 'advanced':
            case 'premium':
                return $this->isAdvancedOrPremiumEventAvailable($event);
            case 'sherpa':
                return $event['splLimitWTransfer']['isAvailable'] && ! $event['isLocked'];
            case 'recovery':
                return $event['splLimitTransfer']['isAvailable'] && ! $event['isLocked'];
            default:
                return false;
        }
    }

    /**
     * Check availability for advanced or premium events
     *
     * @param array $event
     *
     * @return bool
     */
    private function isAdvancedOrPremiumEventAvailable(array $event): bool
    {
        if (is_null($selectedArmor = $event['selectedArmorType'] ?? null)) {
            return false;
        }
        foreach ($event['wowArmors'] as $armor) {
            if ($armor['type'] === $selectedArmor) {
                return $armor['isAvailable'] && ! $event['isLocked'];
            }
        }

        return false;
    }

    /**
     * Get event booster ID
     *
     * @param array $event
     *
     * @return int|null
     */
    private function getEventBoosterId(array $event): ?int
    {
        return $event['booster']['parent']['id'] ?? $event['booster']['id'];
    }

    /**
     * Check item checkboxes and return true if one of them is cfr
     *
     * @param array $item
     *
     * @return bool
     */
    private function isCfr(array $item): bool
    {
        $cfrCheckboxes  = get_option('wv_iscfr_checkboxes', []);
        $itemCheckboxes = $this->getItemCheckboxes($item);
        if (empty($cfrCheckboxes) || empty($itemCheckboxes)) {
            return false;
        }

        foreach ($itemCheckboxes as $slug) {
            $id = $this->getAttributeIdByName($slug);
            if ($cfrCheckboxes[$id] ?? false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get item checkboxes
     *
     * @param array $item
     *
     * @return array
     */
    private function getItemCheckboxes(array $item): array
    {
        return array_keys($item['checkboxes'] ?? []);
    }

    /**
     * Get attribute ID by name
     *
     * @param string $slug
     *
     * @return int
     */
    private function getAttributeIdByName(string $slug): int
    {
        return wc_attribute_taxonomy_id_by_name($slug);
    }

    /**
     * The item has schedule when it is with schedule or shedule (typo in project) id parameter
     *
     * @param array $item
     *
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
     *
     * @return array
     */
    private function fetchEvents(array $item): array
    {
        $query   = $this->getQuery($item);
        $fetcher = new Fetcher();

        return $fetcher->request($query);
    }

    /**
     * Get query for fetching events
     * Select endpoint events/wp when spec-name exists and pa_loot-system is not simple
     * Select only future events because of request parameter isPast has default value false
     *
     * @param array $item
     *
     * @return string
     */
    private function getQuery(array $item): string
    {
        $params = $this->getParamsForQuery($item);
        $attrs  = $this->getItemAttributes($item);

        return 'events/wp?' . http_build_query(array_merge($params, $attrs));
    }

    /**
     * Get parameters for query
     *
     * @param array $item
     *
     * @return array
     */
    private function getParamsForQuery(array $item): array
    {
        return array_filter([
            'isCfr'     => $item['isCfr'] ? 'true' : null,
            'eventType' => $item['event_type_id'],
            'id'        => $this->getEventId($item),
        ]);
    }

    /**
     * Get only needed item attrs data in required format for building query
     *
     * @param array $item
     *
     * @return array
     */
    private function getItemAttributes(array $item): array
    {
        if (isset($item['attributes'])) {
            $itemData = $item['attributes'];
        } else {
            $itemData = $item;
        }
        $this->removeUnnecessaryAttributes($itemData);

        $attrs = [];
        foreach ($itemData as $key => $value) {
            if ($this->isAttribute($key) && ! empty($value)) {
                $attrs[$key] = $value;
            }
        }
        $this->processLootSystem($attrs);

        if (isset($item['spec-name'])) {
            $attrs['spec-name'] = $item['spec-name'];
        } elseif (isset($item['attribute_pa_spec-name'])) {
            $attrs['spec-name'] = $item['attribute_pa_spec-name'];
        }

        return $attrs;
    }

    /**
     * Remove unnecessary attributes from item data
     *
     * @param array &$itemData
     *
     * @return void
     */
    private function removeUnnecessaryAttributes(array &$itemData): void
    {
        unset(
            $itemData['pa_region'],
            $itemData['event_region_slug'],
            $itemData['pa_choose-your-region'],
            $itemData['attribute_pa_choose-your-region'],
            $itemData['attribute_pa_game'],
        );
    }

    /**
     * Check if key is an attribute
     *
     * @param string $key
     *
     * @return bool
     */
    private function isAttribute(string $key): bool
    {
        return explode('_', $key)[0] === 'attribute';
    }

    /**
     * Process loot system in attributes
     *
     * @param array &$attrs
     *
     * @return void
     */
    private function processLootSystem(array &$attrs): void
    {
        if (array_key_exists('attribute_pa_loot-system', $attrs)) {
            $lootSystem              = $attrs['attribute_pa_loot-system'];
            $attrs['pa_loot-system'] = strpos($lootSystem, 'standard') !== false ? 'simple' :
                (strpos($lootSystem, 'advanced') !== false ? 'advanced' :
                    (strpos($lootSystem, 'premium') !== false ? 'premium' : null));
            unset($attrs['attribute_pa_loot-system']);
        }
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
            $response      = wp_remote_request($requestParams['url'], $requestParams['args']);
            if (wp_remote_retrieve_response_code($response) !== 200) {
                $errorMessage = is_wp_error($response) ?
                    $response->get_error_message() :
                    wp_remote_retrieve_response_code($response) . ' ; ' . wp_remote_retrieve_response_message(
                        $response
                    );

                throw new Exception(
                    '[request] Request: ' . $requestParams['url'] . '[response] Response: ' . $errorMessage
                );
            }

            $body   = json_decode(wp_remote_retrieve_body($response), true);
            $result = $body['data'] ?? [];
        } catch (Exception $exception) {
            $business_logger->info($exception->getMessage());
        }

        return $result;
    }

    private function getRequestParams(string $queryParams, array $args): array
    {
        $serviceUrl       = SERVICE_URL;
        $authToken = AUTH_TOKEN;
        if ( ! $authToken) {
            throw new Exception('[authentication] Authentication token unsetted.');
        }
        if ( ! $serviceUrl) {
            throw new Exception('[authentication] Url unsetted.');
        }

        return [
            'url'  => $serviceUrl . '/' . $queryParams,
            'args' => [
                'method'  => 'GET',
                'headers' => [
                    'X-Auth-Token' => $authToken,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json'
                ],
                ...$args,
            ]
        ];
    }
}
