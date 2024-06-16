<?php
/**
 * Validate checkout items with events and remove items with unavailable events
 */
public function validateCheckoutItems(): void
{
    $cartItems = $this->getCartItems();

    foreach ($cartItems as $itemId => $item) {
        $itemData = $this->getCheckoutItemDataForValidation($item);

        if (!$this->isItemWithEventValid($itemData, $itemId)) {
            $this->removeInvalidItemFromCart($itemId);
        }
    }
}

/**
 * Get items from the cart
 *
 * @return array
 */
private function getCartItems(): array
{
    global $woocommerce;
    return $woocommerce->cart->get_cart();
}

/**
 * Remove invalid item from cart and add notice
 *
 * @param string $itemId
 */
private function removeInvalidItemFromCart(string $itemId): void
{
    global $woocommerce;
    $woocommerce->cart->remove_cart_item($itemId);
    wc_add_notice(__('Sorry, the selected time slot is no longer available. Please choose another time on product page.', 'lang'), 'error');
}