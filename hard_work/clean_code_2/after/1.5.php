<?php
/**
 * Get array with estimated time texts for products and variations
 *
 * @param WC_Product $product
 * @return array
 */
private function getEstimatedTimes(WC_Product $product): array
{
    $estimatedTimes = [
        'productEstimatedTimeText' => $this->getProductEstimatedTimeText($product),
        'variationsEstimatedTimeText' => $this->getVariationsEstimatedTimeTexts($product),
    ];

    return $estimatedTimes;
}

/**
 * Get estimated time text for a product
 *
 * @param WC_Product $product
 * @return string
 */
private function getProductEstimatedTimeText(WC_Product $product): string
{
    return esc_html($product->get_meta('_estimated_time_text'));
}

/**
 * Get estimated time texts for product variations
 *
 * @param WC_Product $product
 * @return array
 */
private function getVariationsEstimatedTimeTexts(WC_Product $product): array
{
    $variationsEstimatedTimeText = [];

    if ($product->is_type('variable')) {
        foreach ($product->get_available_variations('objects') as $variation) {
            $text = $variation->get_meta('_estimated_time_text');
            if (!empty($text)) {
                $variationsEstimatedTimeText[$variation->get_id()] = esc_html($text);
            }
        }
    }

    return $variationsEstimatedTimeText;
}