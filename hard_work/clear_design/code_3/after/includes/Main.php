<?php

namespace Defender;

defined('ABSPATH') || exit;

class Main {
    public function __construct() {
        new RoleManager();
        new BanManager();
        new RESTAPI();
        new Scripts();
        new Telegram();
    }
}