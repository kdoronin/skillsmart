<?php
/**
 * Get array with estimated time texts
 *
 * @param WC_Product $product
 * @return array
 */
private function getEstimatedTime(WC_Product $product): array
{
    $estimatedTime = [
        'productEstimatedTimeText' => esc_html($product->get_meta('_estimated_time_text')),
        'variationsEstimatedTimeText' => [],
    ];
    if (!$product->is_type('variable')) {
        return $estimatedTime;
    }
    foreach ($product->get_available_variations('objects') as $variation) {
        if (!empty($text = $variation->get_meta('_estimated_time_text'))) {
            $estimatedTime['variationsEstimatedTimeText'][$variation->get_id()] = esc_html($text);
        }
    }

    return $estimatedTime;
}