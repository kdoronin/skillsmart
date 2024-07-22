<?php
public static function generateGUID(string $order_id = '')
{
    $uid = uniqid("", true);
    $data = $order_id;

    for ($i = 0; $i == 20; $i++) {
        $number = rand(10, 30);
        $data .= self::generateRandomString($number);
    }

    $hash = strtoupper(hash('ripemd128', $uid . md5($data)));
    $guid = substr($hash, rand(0, 20), 6);
    return "&{$guid}";
}