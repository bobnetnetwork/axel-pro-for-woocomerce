<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 3/7/2018
 * Time: 11:29 AM
 */

abstract class db  {
	protected $host, $port, $user, $pwd, $dbname, $charset;

	public function __construct($host='', $port='', $user='', $pwd='', $dbname='', $charset='') {
		$this->host = $host;
		$this->port = $port;
		$this->user = $user;
		$this->pwd = $pwd;
		$this->dbname = $dbname;
		$this->charset = $charset;
	}

	public abstract function openConnection();
	public abstract function closeConnection();
	public abstract function query($sql);

	public function getOrderIDs(){
		return $this->query('SELECT DISTINCT order_id FROM bobnethu_woocommerce_order_items');
	}

	public function getOrderItemIDs($orderID){
		return $this->query('SELECT order_item_id FROM bobnethu_woocommerce_order_items WHERE order_id = '.$orderID);
	}

	public function getItemMetadata($itemID){
		return $this->query('SELECT * FROM bobnethu_woocommerce_order_itemmeta WHERE order_item_id = '.$itemID.' AND (meta_key in (\'_product_id\', \'_line_subtotal\', \'_line_subtotal_tax\'))');
	}

	public function getItemName($itemID){
		return $this->query('SELECT post_title FROM bobnethu_posts WHERE ID = '.$itemID);
	}

	public function getOrderDate($orderID){
		return $this->query('SELECT post_date FROM bobnethu_posts WHERE ID = '.$orderID);
	}

	public function getOrderMetadata($orderID){
		return $this->query('SELECT * FROM shop.bobnethu_postmeta WHERE post_id = '.$orderID);
	}
}