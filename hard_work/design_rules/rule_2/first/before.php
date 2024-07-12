<?php
defined('ABSPATH') or die('Not Authorized!');

class User
{
    private int $ID;

    public function __construct()
    {
        $this->ID = $this->getID();
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

    private function getID(): int
    {
        return get_current_user_id();
    }
}