<?php
/*
Plugin Name:  Axel Pro for Woocommerce WordPress
Plugin URI:   https://bobnet.hu/download/axel-pro-for-woocommerce/
Description:  Axel Por plugin for Woocommerce Wordpress
Version:      20180228
Author:       Bobesz
Author URI:   https://bobnet.hu/
License:      GPLv3
License URI:  https://www.gnu.org/licenses/gpl-3.0.en.html
Text Domain:  axel-pro-for-woocommerce
Domain Path:  /languages
*/

/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/27/2018
 * Time: 8:03 PM
 */


function axel_pro_create_table() {
	global $wpdb;
	global $axel_pro_db_name;
	$wpdb->axel_pro_table_name = $wpdb->prefix . 'axel_pro';

	// create the ECPT metabox database table
	if($wpdb->get_var("show tables like '$wpdb->axel_pro_table_name'") != $wpdb->axel_pro_table_name)
	{

		$sql = "CREATE TABLE " . $wpdb->axel_pro_table_name . " (
		`id` INT NOT NULL AUTO_INCREMENT,
		`orderID` INT NOT NULL,
		`posted` TINYINT NOT NULL,
		PRIMARY KEY (`orderID`),
		INDEX `id` (`id` ASC));
		";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

register_activation_hook(__FILE__,'axel_pro_create_table');

function axel_pro_woocommerce_payment_complete( $order_id ) {
	global $wpdb;
	$wpdb->insert($wpdb->prefix . 'axel_pro', array(
		'orderID' => $order_id,
		'posted' => 0,
	));
}
add_action( 'woocommerce_order_status_completed', 'axel_pro_woocommerce_payment_complete', 10, 1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( 'axel_pro_for_woocommerce' ) ) :

class axel_pro_for_woocommerce
{
    public $version = '2.1.4';
    public $plugin_basename;
    public $legacy_mode;

    protected static $_instance = null;

    /**
     * Main Plugin Instance
     *
     * Ensures only one instance of plugin is loaded or can be loaded.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $f = new functions();

    	$this->plugin_basename = plugin_basename(__FILE__);

        $this->define( 'WPO_AXELPRO_VERSION', $this->version );

        // load the localisation & classes
        add_action( 'plugins_loaded', array( $this, 'translations' ) );
        add_filter( 'load_textdomain_mofile', array( $this, 'textdomain_fallback' ), 10, 2 );
        add_action( 'plugins_loaded', array( $this, 'load_classes' ), 9 );
        add_action( 'in_plugin_update_message-'.$this->plugin_basename, array( $this, 'in_plugin_update_message' ) );

	    $f = new functions();
    }

    /**
     * Define constant if not already set
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Load the translation / textdomain files
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     */
    public function translations() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'axel-pro-for-woocommerce' );
        $dir    = trailingslashit( WP_LANG_DIR );

        $textdomains = array( 'axel-pro-for-woocommerce' );
        if ( $this->legacy_mode_enabled() === true ) {
            $textdomains[] = 'wpo_axelpro';
        }

        /**
         * Frontend/global Locale. Looks in:
         *
         * 		- WP_LANG_DIR/axel-pro-for-woocommerce/axel-pro-for-woocommerce-LOCALE.mo
         * 	 	- WP_LANG_DIR/plugins/axel-pro-for-woocommerce-LOCALE.mo
         * 	 	- axel-pro-for-woocommerce/languages/axel-pro-for-woocommerce-LOCALE.mo (which if not found falls back to:)
         * 	 	- WP_LANG_DIR/plugins/axel-pro-for-woocommerce-LOCALE.mo
         */
        foreach ($textdomains as $textdomain) {
            load_textdomain( $textdomain, $dir . 'axel-pro-for-woocommerce/axel-pro-for-woocommerce' . $locale . '.mo' );
            load_textdomain( $textdomain, $dir . 'plugins/axel-pro-for-woocommerce' . $locale . '.mo' );
            load_plugin_textdomain( $textdomain, false, dirname( plugin_basename(__FILE__) ) . '/languages' );
        }
    }

    /**
     * Maintain backwards compatibility with old translation files
     * Uses old .mo file if it exists in any of the override locations
     */
    public function textdomain_fallback( $mofile, $textdomain ) {
        $plugin_domain = 'axel-pro-for-woocommerce';
        $old_domain = 'axel_pro_for_woocommerce';

        if ($textdomain == $old_domain) {
            $textdomain = $plugin_domain;
            $mofile = str_replace( "{$old_domain}-", "{$textdomain}-", $mofile ); // with trailing dash to target file and not folder
        }

        if ( $textdomain === $plugin_domain ) {
            $old_mofile = str_replace( "{$textdomain}-", "{$old_domain}-", $mofile ); // with trailing dash to target file and not folder
            if ( file_exists( $old_mofile ) ) {
                // we have an old override - use it
                return $old_mofile;
            }

            // prevent loading outdated language packs
            $pofile = str_replace('.mo', '.po', $mofile);
            if ( file_exists( $pofile ) ) {
                // load po file
                $podata = file_get_contents($pofile);
                // set revision date threshold
                $block_before = strtotime( '2017-05-15' );
                // read revision date
                preg_match('~PO-Revision-Date: (.*?)\\\n~s',$podata,$matches);
                if (isset($matches[1])) {
                    $revision_date = $matches[1];
                    if ( $revision_timestamp = strtotime($revision_date) ) {
                        // check if revision is before threshold date
                        if ( $revision_timestamp < $block_before ) {
                            // try bundled
                            $bundled_file = $this->plugin_path() . '/languages/'. basename( $mofile );
                            if (file_exists($bundled_file)) {
                                return $bundled_file;
                            } else {
                                return '';
                            }
                            // delete po & mo file if possible
                            // @unlink($pofile);
                            // @unlink($mofile);
                        }
                    }
                }
            }
        }

        return $mofile;
    }

    /**
     * Load the main plugin classes and functions
     */
    public function includes() {
        // Plugin classes
        include_once( $this->plugin_path() . '/includes/dao/axpfw_address.php' );
        include_once( $this->plugin_path() . '/includes/dao/axpfw_customer.php' );
        include_once( $this->plugin_path() . '/includes/dao/axpfw_item.php' );
        include_once( $this->plugin_path() . '/includes/dao/axpfw_order.php' );
        $this->settings = include_once( $this->plugin_path() . '/includes/service/axpfw_settings.php' );
        //$this->main = include_once( $this->plugin_path() . '/includes/axel_pro_main.php' );
        include_once( $this->plugin_path() . '/includes/service/axpfw_collector.php' );
	    include_once( $this->plugin_path() . '/includes/service/axpfw_db.php' );
	    include_once( $this->plugin_path() . '/includes/service/axpfw_functions.php' );
        include_once( $this->plugin_path() . '/includes/service/axel-pro/axpfw_xml_generator.php' );
	    include_once( $this->plugin_path() . '/includes/service/impl/db/axpfw_pdoDB.php' );
	    include_once( $this->plugin_path() . '/includes/service/impl/db/axpfw_wpDB.php' );
    }

    /**
     * Instantiate classes when woocommerce is activated
     */
    public function load_classes() {
        if ( $this->is_woocommerce_activated() === false ) {
            add_action( 'admin_notices', array ( $this, 'need_woocommerce' ) );
            return;
        }

        if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
            add_action( 'admin_notices', array ( $this, 'required_php_version' ) );
            return;
        }

        // all systems ready - GO!
        $this->includes();
    }

    /**
     * Check if legacy mode is enabled
     */
    public function legacy_mode_enabled() {
        if (!isset($this->legacy_mode)) {
            $debug_settings = get_option( 'wpo_wcpdf_settings_debug' );
            $this->legacy_mode = isset($debug_settings['legacy_mode']);
        }
        return $this->legacy_mode;
    }


    /**
     * Check if woocommerce is activated
     */
    public function is_woocommerce_activated() {
        $blog_plugins = get_option( 'active_plugins', array() );
        $site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option('active_sitewide_plugins' ) ) : array();

        if ( in_array( 'woocommerce/woocommerce.php', $blog_plugins ) || isset( $site_plugins['woocommerce/woocommerce.php'] ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * WooCommerce not active notice.
     *
     * @return string Fallack notice.
     */

    public function need_woocommerce() {
        $error = sprintf( __( ' Axel Pro for Woocommerce WordPress requires %sWooCommerce%s to be installed & activated!' , 'axel-pro-for-woocommerce' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>' );

        $message = '<div class="error"><p>' . $error . '</p></div>';

        echo $message;
    }

    /**
     * PHP version requirement notice
     */

    public function required_php_version() {
        $error = __( ' Axel Pro for Woocommerce WordPress requires PHP 5.3 or higher (5.6 or higher recommended).', 'axel-pro-for-woocommerce' );
        $how_to_update = __( 'How to update your PHP version', 'axel-pro-for-woocommerce' );
        $message = sprintf('<div class="error"><p>%s</p><p><a href="%s">%s</a></p></div>', $error, 'http://docs.wpovernight.com/general/how-to-update-your-php-version/', $how_to_update);

        echo $message;
    }

    //ezeket még át kel írni
    /**
     * Show plugin changes. Code adapted from W3 Total Cache.
     */
    public function in_plugin_update_message( $args ) {
        $transient_name = 'wpo_axelpro_upgrade_notice_' . $args['Version'];

        if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {
            $response = wp_safe_remote_get( '' );

            if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
                $upgrade_notice = self::parse_update_notice( $response['body'], $args['new_version'] );
                set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
            }
        }

        echo wp_kses_post( $upgrade_notice );
    }

    /**
     * Parse update notice from readme file.
     *
     * @param  string $content
     * @param  string $new_version
     * @return string
     */
    private function parse_update_notice( $content, $new_version ) {
        // Output Upgrade Notice.
        $matches        = null;
        $regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $new_version ) . '\s*=|$)~Uis';
        $upgrade_notice = '';


        if ( preg_match( $regexp, $content, $matches ) ) {
            $notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

            // Convert the full version strings to minor versions.
            $notice_version_parts  = explode( '.', trim( $matches[1] ) );
            $current_version_parts = explode( '.', $this->version );

            if ( 3 !== sizeof( $notice_version_parts ) ) {
                return;
            }

            $notice_version  = $notice_version_parts[0] . '.' . $notice_version_parts[1];
            $current_version = $current_version_parts[0] . '.' . $current_version_parts[1];

            // Check the latest stable version and ignore trunk.
            if ( version_compare( $current_version, $notice_version, '<' ) ) {

                $upgrade_notice .= '</p><p class="wpo_axelpro_upgrade_notice">';

                foreach ( $notices as $index => $line ) {
                    $upgrade_notice .= preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line );
                }
            }
        }

        return wp_kses_post( $upgrade_notice );
    }

    /**
     * Get the plugin url.
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
}
endif; // class_exists

/**
 * Returns the main instance of WooCommerce PDF Invoices & Packing Slips to prevent the need to use globals.
 *
 * @since  1.6
 * @return WPO_WCPDF
 */
function axel_pro_for_woocommerce() {
    return axel_pro_for_woocommerce::instance();
}

axel_pro_for_woocommerce(); // load plugin

// legacy class for plugin detecting
if ( !class_exists( 'Axel_Pro_Invoices' ) ) {
    class Axel_Pro_Invoices{
        public static $version;

        public function __construct() {
            self::$version = axel_pro_for_woocommerce()->version;
        }
    }
    new Axel_Pro_Invoices();
}