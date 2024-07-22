<?php
public static function generateGUID(string $order_id = '')
{
    $uid = uniqid("", true);
    $data = $order_id;

    $data = array_reduce(
        range(1, 20),
        function ($carry) {
            $number = rand(10, 30);
            return $carry . self::generateRandomString($number);
        },
        $data
    );

    $hash = strtoupper(hash('ripemd128', $uid . md5($data)));
    $guid = substr($hash, rand(0, 20), 6);
    return "&{$guid}";
}
