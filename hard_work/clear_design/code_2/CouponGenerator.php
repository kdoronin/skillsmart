<?php

class CouponGenerator
{
    /**
     * Create coupons for the pool and return their codes
     *
     * @param int $quantity
     * @param string $poolName
     * @param array $args
     * @return array
     */
    public function generateCouponsPool(int $quantity, string $poolName, array $args = []): array
    {
        $coupons = [];
        for ($i = 0; $i < $quantity; $i++) {
            $coupons[] = $this->getRandomCouponCode();
        }
        $this->createCoupon($coupons, $poolName, $args);
        return $coupons;
    }

    /**
     * Create coupons with requested params
     *
     * @param array $coupons
     * @param string $poolName
     * @param array $args
     */
    private function createCoupon(array $coupons, string $poolName, array $args = []): void
    {
        foreach ($coupons as $coupon) {
            $couponArgs = array(
                'post_title' => $coupon,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'shop_coupon',
                'post_excerpt' => 'Auto generated WV coupon for ' . $poolName . ' pool',
            );
            $couponId = wp_insert_post($couponArgs);
            if (!empty($couponId) && !is_wp_error($couponId)) {
                foreach ($args as $key => $val) {
                    update_post_meta($couponId, $key, $val);
                }
            }
        }
    }

    /**
     * Generate random coupon code
     *
     * @return string
     */
    private function getRandomCouponCode(): string
    {
        $random_coupon = '';
        $length = 12;
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $count = strlen($charset);

        while ($length--) {
            $random_coupon .= $charset[mt_rand(0, $count - 1)];
        }

        $random_coupon = implode('-', str_split(strtoupper($random_coupon), 4));
        $coupon_code = apply_filters('woocommerce_coupon_code', $random_coupon);
        return $coupon_code;
    }
}