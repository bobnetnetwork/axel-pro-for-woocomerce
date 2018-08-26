<?php
/**
 * Created by PhpStorm.
 * User: bobes
 * Date: 2/28/2018
 * Time: 1:35 AM
 */

namespace HU\BOBNET\AXPFW\SERVICE;

use HU\BOBNET\AXPFW\DAO;
use  HU\BOBNET\AXPFW\SERVICE\IMPL\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\SERVICE\\axpfw_collector' ) ) :

class axpfw_collector
{
	private $orders = array();
    private $db;

    public function __construct(){
	    global $wpdb;
	    $this->db = new DB\axpfw_wpDB($wpdb->prefix);
    }

    public function collectOrders(){
        $orderids = $this->db->getOrderIDs();

	    foreach ($orderids as $orderid)
        {
            //order
            $order = new DAO\axpfw_order();
            $order->orderID = $orderid['orderID'];

            if($this->db->isBarion($order->orderID)){
                $bar = $this->db->getBarionpaymentID($order->orderID);
                foreach ($bar as $brow) {
                    $order->barionid = $brow['meta_value'];
                }

            }else if($this->db->isPaypal($order->orderID)){
                $paypal = $this->db->getPaypalpaymentID($order->orderID);
                foreach ($paypal as $prow) {
                    $order->paypalid = $prow['meta_value'];
                }
            }

            $orderitemids =  $this->db->getOrderItemIDs($order->orderID);

            //order items
            foreach ($orderitemids as $orderitemid) {
                $item = new DAO\axpfw_item();

                $itemmeta = $this->db->getItemMetadata($orderitemid['order_item_id']);

                foreach ($itemmeta as $row) {
                    switch ($row['meta_key']) {
                        case '_product_id':
                            $item->itemID = $row['meta_value'];
                            break;
                        case '_line_subtotal':
                            $item->value = $row['meta_value'];
                            break;
                        case '_line_subtotal_tax':
                            $item->tax = $row['meta_value'];
                            break;
                        case '_qty':
                            $item->count = $row['meta_value'];
                            break;
                    }

                }

                $price = $this->db->getItemPrice($item->itemID);
                foreach ($price as $prow){
                    $item->price = $prow['meta_value'];
                }

                $itemname = $this->db->getItemName($item->itemID);
                $item->name = $itemname['0']['post_title'];
                $order->addItem($item);

            }

            //shipping
            if ($this->db->getShipping) {
                //results are empty, do something here
            } else {
                $shipping = $this->db->getOrderShippingMeta($order->orderID);


                foreach ($shipping as $row) {
                    $itemShip = new DAO\axpfw_item();

                    $itemShip->name = "Szállítás - " . $row['order_item_name'];
                    $itemShip->itemID = $row['order_item_id'];

                    $valueandtax = $this->db->getShipValueAndTax($row['order_item_id']);
                    foreach ($valueandtax as $row2) {
                        switch ($row2['meta_key']) {
                            case 'cost':
                                $itemShip->value = $row2['meta_value'];
                                break;
                            case 'total_tax':
                                $itemShip->tax = $row2['meta_value'];
                                break;
                        }
                    }

                    $order->addItem($itemShip);
                }
            }

            $orderdate = $this->db->getOrderDate($order->orderID);
            $order->date = $orderdate['0']['post_date'];

            //customer, other
            $ordermeta = $this->db->getOrderMetadata($order->orderID);

            $shipping_customer = new DAO\axpfw_customer();
            $billing_customer = new DAO\axpfw_customer();

            $shipping_address = new DAO\axpfw_address();
            $billing_address = new DAO\axpfw_address();

            foreach ($ordermeta as $meta)
            {
                switch ($meta['meta_key']) {
                    case '_payment_method':
                        $order->payment_method = $meta['meta_value'];
                        break;
                    case '_billing_first_name':
                        $billing_customer->firstname = $meta['meta_value'];
                        break;
                    case '_billing_last_name':
                        $billing_customer->lastname = $meta['meta_value'];
                        break;
                    case '_billing_company':
                        $billing_customer->company = $meta['meta_value'];
                        break;
                    case '_billing_address_1':
                        $billing_address->address1 = $meta['meta_value'];
                        break;
                    case '_billing_address_2':
                        $billing_address->address2 = $meta['meta_value'];
                        break;
                    case '_billing_city':
                        $billing_address->city = $meta['meta_value'];
                        break;
                    case '_billing_state':
                        $billing_address->state = $meta['meta_value'];
                        break;
                    case '_billing_postcode':
                        $billing_address->postcode = $meta['meta_value'];
                        break;
                    case '_billing_country':
                        $billing_address->country = $meta['meta_value'];
                        break;
                    case '_billing_email':
                        $billing_customer->email = $meta['meta_value'];
                        break;
                    case '_billing_phone':
                        $billing_customer->phone = $meta['meta_value'];
                        break;
                    case '_shipping_first_name':
                        $shipping_customer->firstname = $meta['meta_value'];
                        break;
                    case '_shipping_last_name':
                        $shipping_customer->lastname = $meta['meta_value'];
                        break;
                    case '_shipping_company':
                        $shipping_customer->company = $meta['meta_value'];
                        break;
                    case '_shipping_address_1':
                        $shipping_address->address1 = $meta['meta_value'];
                        break;
                    case '_shipping_address_2':
                        $shipping_address->address2 = $meta['meta_value'];
                        break;
                    case '_shipping_city':
                        $shipping_address->city = $meta['meta_value'];
                        break;
                    case '_shipping_state':
                        $shipping_address->state = $meta['meta_value'];
                        break;
                    case '_shipping_postcode':
                        $shipping_address->postcode = $meta['meta_value'];
                        break;
                    case '_shipping_country':
                        $shipping_address->country = $meta['meta_value'];
                        break;
                    case '_shipping_email':
                        $shipping_customer->email = $meta['meta_value'];
                        break;
                    case '_shipping_phone':
                        $shipping_customer->phone = $meta['meta_value'];
                        break;
                    case '_order_currency':
                        $order->currency = $meta['meta_value'];
                        break;
                    case '_order_tax':
                        $order->tax = $meta['meta_value'];
                        break;
                    case '_order_total':
                        $order->value = $meta['meta_value'];
                        break;
                }
            }

            $shipping_customer->address = $shipping_address;
            $billing_customer->address = $billing_address;

            $order->shipping_customer = $shipping_customer;
            $order->billing_customer = $billing_customer;
            $this->orders[] = $order;
        }

        $pdo = null;
    }

    public function getOrders()
    {
        return $this->orders;
    }

	public function setPostedOrdesStatus(){
    	foreach ($this->orders as &$order){
			$this->db->setPosted($order->orderID);
	    }
	}

}
endif; // class_exists

return new axpfw_collector();