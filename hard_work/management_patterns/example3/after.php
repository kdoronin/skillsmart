<?php
public function removeExpiredBans(): void
{
    $banned = get_option('banned_data');
    $current_time = current_time('U', true);

    $banned = array_filter($banned, function($value) use ($current_time) {
        return $current_time <= $value['expired'];
    });

    update_option('banned_data', $banned);
}