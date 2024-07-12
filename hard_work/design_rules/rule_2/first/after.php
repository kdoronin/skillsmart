<?php

defined('ABSPATH') or die('Not Authorized!');

class User
{
    private int $ID;

    public function __construct(int $ID = 0)
    {
        $this->ID = $ID;
    }

    public function prepare(): array
    {
        if ($this->ID === 0) {
            return [];
        }

        return [
            'user_id' => strval($this->ID),
        ];
    }
}