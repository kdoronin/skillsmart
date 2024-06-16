<?php
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