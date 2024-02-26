<?php

namespace GA4\Analytics;

defined('ABSPATH') or die('Not Authorized!');

/**
 * Class Item
 * @package GA4\Analytics
 * @description Class for create GA4 Item from WooCommerce Product
 * Use WooCommerce Product ID to create Item. Also you can add a few items with the same ID but different quantity.
 * Class collected categories data automatically. It uses WooCommerce Product Categories for that.
 * You can use addData method to add additional data to Item.
 * With updateQuantity method you can change quantity of Item.
 * Google Analytics 4 calculated value by itself. But you can use calculateValue method to calculate it manually
 * (in plugin I use it in Items class to calculate total value of all items in cart).
 * Use prepareItem method to get Item data for GA4.
 */
class Item
{
    public string $item_id;
    private int $quantity;
    private array $data;
    public function __construct(string $item_id, int $quantity = 1)
    {
        $this->item_id = $item_id;
        $this->quantity = $quantity;
        $this->data = [];
        $this->addData($this->prepareCategories());
    }

    public function prepareItem(): array
    {
        $this->data['quantity'] = $this->quantity;
        return $this->data;
    }

    public function updateQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function addData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    public function calculateValue(): float
    {
        return $this->data['price'] * $this->quantity;
    }

    private function prepareCategories(): array
    {
        $categories = [];
        $productCategories = get_the_terms($this->item_id, 'product_cat');
        $number = 1;
        while ($productCategories && !is_wp_error($productCategories)) {
            $productCategory = array_shift($productCategories);
            if ($number === 1) {
                $categories['item_category'] = $productCategory->name;
                $categories['item_category_id'] = $productCategory->term_id;
                $number++;
                continue;
            }
            $categories['item_category' . $number] = $productCategory->name;
            $categories['item_category_id' . $number] = $productCategory->term_id;
            $number++;
        }
        return $categories;
    }
}
