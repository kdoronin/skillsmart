<?php

class ProductParams {
    public $product_id;
    public $variation_id;
    public $items;

    public function __construct($product_id, $variation_id, $items) {
        $this->product_id = $product_id;
        $this->variation_id = $variation_id;
        $this->items = $items;
    }
}

class CartParams {
    public $cart_item_data;
    public $quantity;

    public function __construct($cart_item_data, $quantity) {
        $this->cart_item_data = $cart_item_data;
        $this->quantity = $quantity;
    }
}

class ConditionParams {
    public $id;
    public $item;
    public $posted;
    public $group_id;
    public $group;

    public function __construct($id, $item, $posted, $group_id, $group) {
        $this->id = $id;
        $this->item = $item;
        $this->posted = $posted;
        $this->group_id = $group_id;
        $this->group = $group;
    }
}

function pewc_get_conditional_field_visibility(ProductParams $productParams, CartParams $cartParams, ConditionParams $conditionParams) {
    if (empty($conditionParams->posted)) {
        $conditionParams->posted = $_POST;
    }

    if (!pewc_is_group_visible($conditionParams->group_id, $conditionParams->group, $conditionParams->posted)) {
        return false;
    }

    $cart_item = pewc_get_cart_item_by_extras($productParams->product_id, $productParams->variation_id, $cartParams->cart_item_data);
    $line_total = isset($cart_item['line_total']) ? $cart_item['line_total'] : false;

    if (!$line_total) {
        $line_total = isset($cartParams->cart_item_data['product_extras']['price_with_extras'])
            ? $cartParams->cart_item_data['product_extras']['price_with_extras'] * $cartParams->quantity
            : false;
    }

    $conditions = pewc_get_field_conditions($conditionParams->item, $productParams->product_id);

    if (empty($conditions)) {
        return true;
    }

    $rules = pewc_get_field_conditional($conditionParams->item, $productParams->product_id);
    $is_visible = $rules['action'] === 'show' ? false : true;

    $rules_obtain = ($rules['match'] == 'all')
        ? check_all_conditions($conditions, $productParams, $cartParams, $conditionParams, $line_total)
        : check_any_conditions($conditions, $productParams, $cartParams, $conditionParams, $line_total);

    return apply_filters(
        'pewc_get_conditional_field_visibility',
        $productParams,
        $cartParams,
        $conditionParams
    );
}