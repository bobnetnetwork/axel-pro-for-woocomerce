<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/27/2018
 * Time: 9:30 PM
 */

namespace NETWORK\BOBNET\AXPFW\DAO;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\NETWORK\\BOBNET\\AXPFW\\DAO\\axpfw_address' ) ) :

class axpfw_address
{
    private $postcode, $country, $address1, $address2, $city, $state;

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
endif; // class_exists

return new axpfw_address();