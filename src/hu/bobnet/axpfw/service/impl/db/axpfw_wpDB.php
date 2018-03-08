<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 3/7/2018
 * Time: 11:29 AM
 */

namespace HU\BOBNET\AXPFW\SERVICE\IMPL\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\SERVICE\\IMPL\\DB\\axpfw_wpDB' ) ) :

class axpfw_wpDB extends axpfw_db{

	public function openConnection() {

	}

	public function closeConnection() {

	}

	public function query( $sql ) {
		global $wpdb;

		return $wpdb->get_results($sql, ARRAY_A);
	}
}
endif; // class_exists

return new axpfw_wpDB();