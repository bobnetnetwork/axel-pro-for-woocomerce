<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/27/2018
 * Time: 9:30 PM
 */

namespace HU\BOBNET\AXPFW\DAO;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\DAO\\axpfw_customer' ) ) :

class axpfw_customer
{
    private $company, $firstname, $lastname, $phone, $email, $address;

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

return new axpfw_customer();