<?php

namespace GA4\Events;

use WOOMULTI_CURRENCY_Data;

defined('ABSPATH') or die('Not Authorized!');

abstract class Event
{
    protected string $name;
    protected EventParams $params;

    public function __construct(string $name, EventParams $params)
    {
        $this->params = $params;
        $this->name = $name;
    }

    abstract public function prepare(): array;

    protected function currentCurrency(): string
    {
        if(!class_exists('WOOMULTI_CURRENCY_Data')) {
            return 'USD';
        }
        $setting = WOOMULTI_CURRENCY_Data::get_ins();
        $currentCurrency = $setting->get_current_currency();
        return $currentCurrency ?? 'USD';
    }

}