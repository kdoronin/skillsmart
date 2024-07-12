<?php

use \GA4\Analytics\Item;
use \GA4\Analytics\Items;
use \GA4\Containers\Container;

defined('ABSPATH') or die('Not Authorized!');

class WCCart extends Container
{
    private \WC_Cart $cart;
    private string $type;

    public function __construct(\WC_Cart $cart, string $type)
    {
        $this->cart = $cart;
        $this->type = $type;
    }

    public function getItems(): Items
    {
        $items = new Items();
        foreach ($this->cart->get_cart_contents() as $key => $itemData) {
            if ($this->validateItemData($itemData)) {
                $items->append($this->getItem($key));
            }
        }

        return $items;
    }

    public function getItem(string $item_id): Item
    {
        if ($this->type === 'cart_remove') {
            $itemData = $this->cart->removed_cart_contents[$item_id];
        } else {
            $itemData = $this->cart->cart_contents[$item_id];
        }
        if ($itemData === null || !$this->validateItemData($itemData)) {
            throw new \InvalidArgumentException('Invalid item data.');
        }
        $quantity = $itemData['quantity'] ?? 1;
        $item = new Item(strval($itemData['product_id']), $quantity);
        $item->addData($this->itemData($itemData));

        return $item;
    }

    public function cartHasItem(string $item_id): bool
    {
        return isset($this->cart->cart_contents[$item_id]);
    }

    private function itemData(array $itemData): array
    {
        if (!$this->validateItemData($itemData)) {
            throw new \InvalidArgumentException('Invalid item data.');
        }
        return [
            'item_id'      => strval($itemData['product_id']),
            'item_name'    => $this->itemName(intval($itemData['product_id'])),
            'item_brand'   => $itemData['pa_game'] ?? '',
            'item_variant' => strval($itemData['variation_id']),
            'price'        => $itemData['line_total'] / $itemData['quantity']
        ];
    }

    public function cartCoupons(): array
    {
        return $this->getCoupons($this->cart->get_applied_coupons());
    }

    /**
     * Validates the item data.
     *
     * @param array $itemData
     * @return bool
     */
    private function validateItemData(array $itemData): bool
    {
        $requiredKeys = ['product_id', 'quantity', 'line_total'];
        foreach ($requiredKeys as $key) {
            if (!isset($itemData[$key])) {
                return false;
            }
        }

        return is_int($itemData['product_id']) && is_int($itemData['quantity']) && is_numeric($itemData['line_total']);
    }

}