<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 3/7/2018
 * Time: 11:29 AM
 */

namespace HU\BOBNET\AXPFW\SERVICE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\SERVICE\\axpfw_db' ) ) :

abstract class axpfw_db  {
	protected $host, $port, $user, $pwd, $dbname, $charset, $prefix;

	public function __construct($prefix, $host='', $port='', $user='', $pwd='', $dbname='', $charset='') {
		$this->host = $host;
		$this->port = $port;
		$this->user = $user;
		$this->pwd = $pwd;
		$this->dbname = $dbname;
		$this->charset = $charset;
		$this->prefix = $prefix;
	}

	public abstract function openConnection();
	public abstract function closeConnection();
	public abstract function query($sql);

	public function getOrderIDs(){
		return $this->query('SELECT orderID FROM '.$this->prefix.'axel_pro WHERE posted = 0');
	}

	public function getOrderItemIDs($orderID){
		return $this->query('SELECT order_item_id FROM '.$this->prefix.'woocommerce_order_items WHERE order_id = '.$orderID);
	}

	public function getItemMetadata($itemID){
		return $this->query('SELECT * FROM '.$this->prefix.'woocommerce_order_itemmeta WHERE order_item_id = '.$itemID.' AND (meta_key in (\'_product_id\', \'_line_subtotal\', \'_line_subtotal_tax\'))');
	}

	public function getItemName($itemID){
		return $this->query('SELECT post_title FROM '.$this->prefix.'posts WHERE ID = '.$itemID);
	}

	public function getOrderDate($orderID){
		return $this->query('SELECT post_date FROM '.$this->prefix.'posts WHERE ID = '.$orderID);
	}

	public function getOrderMetadata($orderID){
		return $this->query('SELECT * FROM '.$this->prefix.'postmeta WHERE post_id = '.$orderID);
	}

	public function setPosted($orderID){
		return $this->query('UPDATE  '.$this->prefix.'axel_pro SET posted = 1 WHERE orderID like '. $orderID);
	}
}
endif; // class_exists

return new axpfw_db();