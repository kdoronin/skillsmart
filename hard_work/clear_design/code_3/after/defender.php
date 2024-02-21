<?php
/**
 * Plugin Name:       Defender
 * Description:       Defend checkout form
 * Version:           0.1.2
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */


declare(strict_types=1);

use Defender\Main;

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/main.php';
require_once __DIR__ . '/includes/role-manager.php';
require_once __DIR__ . '/includes/ban-manager.php';
require_once __DIR__ . '/includes/rest-api.php';
require_once __DIR__ . '/includes/scripts.php';
require_once __DIR__ . '/includes/telegram.php';

$defender = new Main();