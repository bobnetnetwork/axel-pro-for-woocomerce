<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/28/2018
 * Time: 12:52 PM
 */

namespace HU\BOBNET\AXPFW\SERVICE;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\SERVICE\\axpfw_settings' ) ) :

class axpfw_settings
{
	public $options_page_hook_bnn, $options_page_hook;

	function __construct()	{
		// Settings menu item
		add_action( 'admin_menu', array( $this, 'menu' ) ); // Add menu.
		// Links on plugin page
		add_filter( 'plugin_action_links_'.axel_pro_for_woocommerce()->plugin_basename, array( $this, 'add_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_support_links' ), 10, 2 );
	}

	public function menu() {
		/*$this->options_page_hook_bnn = add_menu_page(
			__( 'BobNET Network', 'axel-pro-for-woocommerce' ),
			__( 'BobNET Network', 'axel-pro-for-woocommerce' ),
			'manage_bobnet',
			'bnn',
			'wpo_axel_pro_options_page',
			plugin_dir_url(__FILE__) . 'assets/images/icon-128x128.png'
		);

		$this->options_page_hook = add_submenu_page(
			'bnn',
			__( 'Axel Pro', 'axel-pro-for-woocommerce' ),
			__( 'Axel Pro', 'axel-pro-for-woocommerce' ),
			'manage_woocommerce',
			'wpo_axel_pro_options_page',
			array( $this, 'settings_page' )
		);*/
	}

	/**
	 * Add various support links to plugin page
	 * after meta (version, authors, site)
	 */
	public function add_support_links( $links, $file ) {
		if ( $file == axel_pro_for_woocommerce()->plugin_basename ) {
			$row_meta = array(
				'docs'    => '<a href="http://docs.wpovernight.com/woocommerce-pdf-invoices-packing-slips/" target="_blank" title="' . __( 'Documentation', 'axel-pro-for-woocommerce' ) . '">' . __( 'Documentation', 'axel-pro-for-woocommerce' ) . '</a>',
				'support' => '<a href="https://wordpress.org/support/plugin/woocommerce-pdf-invoices-packing-slips" target="_blank" title="' . __( 'Support Forum', 'axel-pro-for-woocommerce' ) . '">' . __( 'Support Forum', 'axel-pro-for-woocommerce' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Add settings link to plugins page
	 */
	public function add_settings_link( $links ) {
		$action_links = array(
			'settings' => '<a href="admin.php?page=wpo_axel_pro_options_page">'. __( 'Settings', 'woocommerce' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	public function settings_page() {
		$settings_tabs = apply_filters( 'wpo_axelpro_settings_tabs', array (
				'general'	=> __('General', 'axel-pro-for-woocommerce' ),
				'documents'	=> __('Documents', 'axel-pro-for-woocommerce' ),
			)
		);

		// add status tab last in row
		$settings_tabs['debug'] = __('Status', 'axel-pro-for-woocommerce' );

		$active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'general';
		$active_section = isset( $_GET[ 'section' ] ) ? sanitize_text_field( $_GET[ 'section' ] ) : '';

		include(axel_pro_for_woocommerce()->plugin_path().'/includes/views/axel_pro_settings_page.php');
	}
}
endif; // class_exists

return new axpfw_settings();