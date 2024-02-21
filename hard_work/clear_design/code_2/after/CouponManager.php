<?php

class CouponManager
{
    private int $quantity;
    private string $poolName;
    private array $args = [];

    const LENGTH = 12;

    public function __construct(int $quantity, string $poolName, array $args = [])
    {
        $this->quantity = $quantity;
        $this->poolName = $poolName;
        $this->args = $args;
    }

    public function generateCouponsPool(): void
    {
        for ($i = 0; $i < $this->quantity; $i++) {
            $this->createCoupon(self::LENGTH);
        }
    }

    private function createCoupon(int $lenght):void
    {
        $coupon = new Coupon($lenght, $this->poolName);
        $couponId = wp_insert_post((array)$coupon);
        if (!empty($couponId) && !is_wp_error($couponId)) {
            foreach ((array)$coupon as $key => $val) {
                update_post_meta($couponId, $key, $val);
            }
        }
    }
}