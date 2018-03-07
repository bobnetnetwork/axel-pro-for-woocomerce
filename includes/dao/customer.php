<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/27/2018
 * Time: 9:30 PM
 */

class customer
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