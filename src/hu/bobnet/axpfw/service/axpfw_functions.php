<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 3/7/2018
 * Time: 2:42 PM
 */

namespace HU\BOBNET\AXPFW\SERVICE;

use HU\BOBNET\AXPFW\SERVICE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\SERVICE\\axpfw_functions' ) ) :

class axpfw_functions {

	public function __construct() {
		add_action('init', array( $this,'my_custom_rss_init'));
		add_filter( 'feed_content_type', array($this, 'my_custom_rss_content_type'), 10, 2);
	}

	/* Add the feed. */
	static function my_custom_rss_init(){
		add_feed('axelpro', 'my_custom_rss');
	}

	/* Filter the type, this hook wil set the correct HTTP header for Content-type. */
	static function my_custom_rss_content_type( $content_type, $type ) {
		if ( 'my_custom_feed' === $type ) {
			return feed_content_type( 'rss2' );
		}
		return $content_type;
	}

	/* Show the RSS Feed on domain.com/?feed=my_custom_feed or domain.com/feed/my_custom_feed. */
	function my_custom_rss() {
		header("Content-Type: application/xml; charset=utf-8");
		$col = new SERVICE\collector();
		$col->collectOrders();

		$axel = new SERVICE\axelProXML($col->getOrders());
		$axel->generateXML();
		print ($axel->getXML());
		$col->setPostedOrdesStatus();
	}

}
endif; // class_exists