<?php
/**
 * WP Multilang General Settings
 *
 * @category    Admin
 * @package     WPM/Admin
 * @author   Valentyn Riaboshtan
 */

namespace WPM\Includes\Admin\Settings;
use WPM\Includes\Admin\WPM_Admin_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPM_Settings_General.
 */
class WPM_Settings_General extends WPM_Settings_Page implements SettingsPage {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'wp-multilang' );

		parent::__construct();

		add_filter( 'wpm_general_settings', array( $this, 'add_uninstall_setting' ) );
		add_action( 'wpm_update_options_general', array( $this, 'update_wplang' ) );
	}

	/**
	 * Add uninstall settings only for Super Admin
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	public function add_uninstall_setting( $settings ) {

		if ( ! is_multisite() || ( is_main_site() ) ) {
			$settings[] = array(
				'title' => __( 'Uninstalling', 'wp-multilang' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'uninstall_options',
			);

			$settings[] = array(
				'title'   => __( 'Delete translations', 'wp-multilang' ),
				'desc'    => __( 'Delete translations when uninstalling plugin (some translations may not be deleted and you must delete them manually).', 'wp-multilang' ),
				'id'      => 'wpm_uninstall_translations',
				'default' => 'no',
				'type'    => 'checkbox',
			);

			$settings[] = array( 'type' => 'sectionend', 'id' => 'uninstall_options' );
		}

		return $settings;
	}

	/**
	 * Save settings.
	 */
	public function save() {
        $visitor = new GetSettings();
		$settings = $this->accept($visitor);

		WPM_Admin_Settings::save_fields( $settings );
	}

	/**
	 * Update WPLANG option
	 */
	public function update_wplang() {
		$value     = WPM_Admin_Settings::get_option( 'wpm_site_language' );
		$languages = wpm_get_languages();
		$locale    = $languages[ $value ]['translation'];
		update_option( 'WPLANG', 'en_US' !== $locale ? $locale : '' );
	}

    public function accept(Visitor $visitor): string
    {
        return $visitor->generalPage($this);
    }
}
