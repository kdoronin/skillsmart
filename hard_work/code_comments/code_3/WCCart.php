<?php

namespace GA4\Containers;

use GA4\Analytics\Item;
use GA4\Analytics\Items;

defined('ABSPATH') or die('Not Authorized!');

/**
 * Class WCCart
 * @package GA4\Containers
 * @description Class-converter WooCommerce cart to GA4 cart
 * You can collect all current Item data with getItems method. It collected data from WC_Cart object.
 * The most important method is getItem. It uses special unique $item_id,
 * that provided WooCommerce Actions when something added or removed from cart.
 * This method returns Item object with all necessary data.
 * You can use cartCoupons method to get all applied coupons.
*/
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
            $items->append($this->getItem($key));
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
        $item = new Item(strval($itemData['product_id']), $itemData['quantity']);
        $item->addData($this->itemData($itemData));

        return $item;
    }

    private function itemData(array $itemData): array
    {
        return [
            'item_id'      => strval($itemData['product_id']),
            'item_name'    => $this->itemName(intval($itemData['product_id'])),
            'item_brand'   => $itemData['pa_game'],
            'item_variant' => strval($itemData['variation_id']),
            'price'        => $itemData['line_total'] / $itemData['quantity']
        ];
    }

    public function cartCoupons(): array
    {
        return $this->getCoupons($this->cart->get_applied_coupons());
    }

}