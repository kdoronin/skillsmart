<?php

namespace WPM\Includes\Admin\Settings;

class GetSettings implements Visitor
{
    use PageSettingsTrait, AdditionalPageTrait, LanguagesPageTrait, GeneralPageTrait;
}

trait PageSettingsTrait
{
    public function pageSettings(WPM_Settings_Page $settingsPage): string
    {
        return apply_filters( 'wpm_get_settings_' . $settingsPage->id, array() );
    }
}

trait AdditionalPageTrait
{
    public function additionalPage(WPM_Settings_Additional $additionalSettingsPage): string
    {
        // Hide the save button
        $GLOBALS['hide_save_button'] = true;

        $settings = apply_filters( 'wpm_' . $additionalSettingsPage->id . '_settings', array(

            array( 'title' => __( 'Actions', 'wp-multilang' ), 'type' => 'title', 'desc' => '', 'id' => 'additional_options' ),

            array(
                'title' => __( 'Installed localizations', 'wp-multilang' ),
                'id'    => 'wpm_installed_localizations',
                'type'  => 'localizations',
            ),

            array(
                'title' => __( 'qTranslate import', 'wp-multilang' ),
                'id'    => 'wpm_qtx_import',
                'type'  => 'qtx_import',
            ),

            array( 'type' => 'sectionend', 'id' => 'additional_options' ),

        ) );

        return apply_filters( 'wpm_get_settings_' . $additionalSettingsPage->id, $settings );
    }
}

trait LanguagesPageTrait
{
    public function languagesPage(WPM_Settings_Languages $languagesSettingsPage): string
    {
        $settings = apply_filters( 'wpm_' . $languagesSettingsPage->id . '_settings', array(

            array( 'title' => __( 'Languages', 'wp-multilang' ), 'type' => 'title', 'desc' => '', 'id' => 'languages_options' ),

            array(
                'title' => __( 'Installed languages', 'wp-multilang' ),
                'id'    => 'wpm_languages',
                'type'  => 'languages',
            ),

            array( 'type' => 'sectionend', 'id' => 'languages_options' ),

        ) );

        return apply_filters( 'wpm_get_settings_' . $languagesSettingsPage->id, $settings );
    }
}

trait GeneralPageTrait
{
    public function generalPage(WPM_Settings_General $generalSettingsPage): string
    {
        $languages = wpm_get_languages();

        $language_options = array();
        foreach ( $languages as $code => $language ) {
            $language_options[ $code ] = $language['name'];
        }

        $settings = apply_filters( 'wpm_general_settings', array(

            array(
                'title' => __( 'General options', 'wp-multilang' ),
                'type'  => 'title',
                'desc'  => sprintf( __( 'Read <a href="%s" target="_blank">Google guidelines</a> before.', 'wp-multilang' ), esc_url( 'https://support.google.com/webmasters/answer/182192?hl=' . wpm_get_user_language() ) ),
                'id'    => 'general_options'
            ),

            array(
                'title'    => __( 'Site Language', 'wp-multilang' ),
                'desc'     => __( 'Set default site language.', 'wp-multilang' ),
                'id'       => 'wpm_site_language',
                'default'  => wpm_get_default_language(),
                'type'     => 'select',
                'class'    => 'wpm-enhanced-select',
                'css'      => 'min-width: 350px;',
                'options'  => $language_options,
            ),

            array(
                'title'   => __( 'Show untranslated', 'wp-multilang' ),
                'desc'    => __( 'Show untranslated strings on language by default.', 'wp-multilang' ),
                'id'      => 'wpm_show_untranslated_strings',
                'default' => 'yes',
                'type'    => 'checkbox',
            ),

            array(
                'title'   => __( 'Browser redirect', 'wp-multilang' ),
                'desc'    => __( 'Use redirect to user browser language in first time.', 'wp-multilang' ),
                'id'      => 'wpm_use_redirect',
                'default' => 'no',
                'type'    => 'checkbox',
            ),

            array(
                'title'   => __( 'Use prefix', 'wp-multilang' ),
                'desc'    => __( 'Use prefix for language by default.', 'wp-multilang' ),
                'id'      => 'wpm_use_prefix',
                'default' => 'no',
                'type'    => 'checkbox',
            ),

            array( 'type' => 'sectionend', 'id' => 'general_options' ),

        ) );

        return apply_filters( 'wpm_get_settings_' . $generalSettingsPage->id, $settings );
    }
}
