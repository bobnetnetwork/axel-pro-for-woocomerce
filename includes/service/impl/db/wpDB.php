<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 3/7/2018
 * Time: 11:29 AM
 */

class wordpressdbquerry extends db{

	public function openConnection() {

	}

	public function closeConnection() {

	}

	public function query( $sql ) {
		global $wpdb;

		return $wpdb->get_results($sql, ARRAY_A);
	}
}