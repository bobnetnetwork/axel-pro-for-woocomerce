<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/27/2018
 * Time: 9:14 PM
 */

class item
{
    private $itemID, $tax, $value, $name;

    public function __construct(){

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