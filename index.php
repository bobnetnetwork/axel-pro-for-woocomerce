<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/27/2018
 * Time: 8:03 PM
 */

include_once "dao/order.php";
include_once "dao/customer.php";
include_once "dao/address.php";
include_once "dao/item.php";
include_once "service/axelProXML.php";
include_once "service/collector.php";

$collector = new collector();
$collector->collectOrders();

$xml = new axelProXML($collector->getOrders());
$xml->generateXML();