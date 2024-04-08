<?php

namespace WPM\Includes\Admin\Settings;

interface Visitor
{
    public function pageSettings(WPM_Settings_Page $settingsPage): string;
    public function additionalPage(WPM_Settings_Additional $additionalSettingsPage): string;
    public function languagesPage(WPM_Settings_Languages $languagesSettingsPage): string;
    public function generalPage(WPM_Settings_General $generalSettingsPage): string;
}