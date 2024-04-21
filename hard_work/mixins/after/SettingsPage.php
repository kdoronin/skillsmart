<?php

namespace WPM\Includes\Admin\Settings;

interface SettingsPage
{
    public function accept(Visitor $visitor);
}