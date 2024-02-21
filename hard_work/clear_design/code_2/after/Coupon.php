<?php

class Coupon
{
    private int $length;

    private string $code;

    const CHARSET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    private string $poolName;

    public function __construct(int $length, string $poolName)
    {
        $this->length = $length;
        $this->poolName = $poolName;
        $this->code = $this->getRandomCouponCode();
    }

    public function __toArray(): array
    {
        return [
            'post_title' => $this->code,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon',
            'post_excerpt' => 'Auto generated WV coupon for ' . $this->poolName . ' pool',
        ];
    }

    private function getRandomCouponCode(): string
    {
        $random_coupon = '';
        $count = strlen(self::CHARSET);

        while ($this->length--) {
            $random_coupon .= self::CHARSET[mt_rand(0, $count - 1)];
        }

        $random_coupon = implode('-', str_split(strtoupper($random_coupon), 4));
        return apply_filters('woocommerce_coupon_code', $random_coupon);
    }

}