<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/27/2018
 * Time: 9:14 PM
 */

namespace HU\BOBNET\AXPFW\DAO;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\DAO\\axpfw_order' ) ) :

class axpfw_order
{
    private $orderID, $billing_customer, $shipping_customer, $date, $payment_method, $value, $tax, $currency, $barionid;
    private $items = array();

    public function __construct(){
    }

    public function addItem($item){
        $this->items[] = $item;
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }
}
endif; // class_exists

return new axpfw_order();