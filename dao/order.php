<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/27/2018
 * Time: 9:14 PM
 */

class order
{
    private $orderID, $billing_customer, $shipping_customer, $date, $payment_method, $value, $tax, $currency;
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