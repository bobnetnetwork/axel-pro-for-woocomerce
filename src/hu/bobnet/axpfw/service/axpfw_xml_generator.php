<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/28/2018
 * Time: 1:29 AM
 */

namespace HU\BOBNET\AXPFW\SERVICE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\HU\\BOBNET\\AXPFW\\SERVICE\\axpfw_xml_generator' ) ) :

class axpfw_xml_generator
{
    private $orders, $xml;

    public function __construct($orders){
        $this->orders = $orders;
    }

    public function generateXML()
    {
        $AXELPRO_IMP_TRANS = new SimpleXMLElement('<AXELPRO_IMP_TRANS/>');// VERSION="1.1"
        $AXELPRO_IMP_TRANS->addAttribute('VERSION', '1.1');

        foreach ($this->orders as &$order) {
            $TRANS = $AXELPRO_IMP_TRANS->addChild('TRANS');
            $TRANS_HEAD = $TRANS->addChild('TRANS_HEAD');
            $IMG_TYPE = $TRANS_HEAD->addChild('IMG_TYPE', 3);
            $IMG_DATETIME = $TRANS_HEAD->addChild('IMG_DATETIME', $order->date);
            $IMG_FULFILMENT_DATE = $TRANS_HEAD->addChild('IMG_FULFILMENT_DATE', $order->date);
            $IMG_DEADLINE_DATE = $TRANS_HEAD->addChild('IMG_DEADLINE_DATE', $order->date);
            $IMG_CUSTOMER_NAME = $TRANS_HEAD->addChild('IMG_CUSTOMER_NAME', $order->billing_customer->lastname.' '.$order->billing_customer->firstname);
            $IMG_CUSTOMER_ADDRESS = $TRANS_HEAD->addChild('IMG_CUSTOMER_ADDRESS', $order->billing_customer->address->postcode.' '.$order->billing_customer->address->city.', '.$order->billing_customer->address->address1.' '.$order->billing_customer->address->address2);
            $IMG_CUSTOMER_OTHER = $TRANS_HEAD->addChild('IMG_CUSTOMER_OTHER');
            $IMG_POST_NAME = $TRANS_HEAD->addChild('IMG_POST_NAME', $order->shipping_customer->lastname.' '.$order->shipping_customer->firstname);
            $IMG_POST_ADDRESS = $TRANS_HEAD->addChild('IMG_POST_ADDRESS', $order->shipping_customer->address->postcode.' '.$order->shipping_customer->address->city.', '.$order->shipping_customer->address->address1.' '.$order->shipping_customer->address->address2);
            $IMG_PAY_NAME = $TRANS_HEAD->addChild('IMG_PAY_NAME', $order->payment_method);
            $IMG_CURR = $TRANS_HEAD->addChild('IMG_CURR', $order->currency);
            $IMG_RATE = $TRANS_HEAD->addChild('IMG_RATE', 1);
            $IMG_PRICE_TYPE = $TRANS_HEAD->addChild('IMG_PRICE_TYPE', 1);
            $IMG_DISCOUNT = $TRANS_HEAD->addChild('IMG_DISCOUNT', 0);
            $IMG_COPIES = $TRANS_HEAD->addChild('IMG_COPIES', 2);
            $IMG_COMMENT = $TRANS_HEAD->addChild('IMG_COMMENT');
            $IMG_IS_MOVE = $TRANS_HEAD->addChild('IMG_IS_MOVE', 1);
            $IMG_IS_PAID = $TRANS_HEAD->addChild('IMG_IS_PAID', 1);
            $IMG_LANG = $TRANS_HEAD->addChild('IMG_LANG', 0);
            $IMG_OTHER = $TRANS_HEAD->addChild('IMG_OTHER');
            $IMG_STORNO = $TRANS_HEAD->addChild('IMG_STORNO', 0);
            $IMG_IS_ADVANCE = $TRANS_HEAD->addChild('IMG_IS_ADVANCE', 0);
            $IMG_IS_CORRECTION = $TRANS_HEAD->addChild('IMG_IS_CORRECTION', 0);
            $IMG_ENVELOPE = $TRANS_HEAD->addChild('IMG_ENVELOPE', 0);
            $IMG_COMPANY_PLUS = $TRANS_HEAD->addChild('IMG_COMPANY_PLUS', 1);

            $TRANS_ITEMS = $TRANS->addChild('TRANS_ITEMS');

            $items = $order->items;
            $i = 1;

            foreach ($items as &$item) {
                $TRANS_ITEM = $TRANS_ITEMS->addChild('TRANS_ITEM');
                $ITM_NAME = $TRANS_ITEM->addChild('ITM_NAME', $item->name);
                $ITM_PRICE_PRICE = $TRANS_ITEM->addChild('ITM_PRICE_PRICE', $item->value);
                $ITM_PRICE_DISCOUNT = $TRANS_ITEM->addChild('ITM_PRICE_DISCOUNT', $item->value);
                $ITM_PRICE_ORIG = $TRANS_ITEM->addChild('ITM_PRICE_ORIG', $item->value);
                $ITM_PRICE_VAT_SHORT = $TRANS_ITEM->addChild('ITM_PRICE_VAT_SHORT', 27);
                $ITM_DATETIME = $TRANS_ITEM->addChild('ITM_DATETIME', $order->date);
                $ITM_AMOUNT = $TRANS_ITEM->addChild('ITM_AMOUNT', 1);
                $ITM_UNIT = $TRANS_ITEM->addChild('ITM_UNIT', 'db');
                $ITM_VTSZSZJ = $TRANS_ITEM->addChild('ITM_VTSZSZJ');
                $ITM_ORD = $TRANS_ITEM->addChild('ITM_ORD', $i);
                $ITM_COMMENT = $TRANS_ITEM->addChild('ITM_COMMENT');
                ++$i;
            }
        }
        //print($AXELPRO_IMP_TRANS->asXML());
	    $this->xml = $AXELPRO_IMP_TRANS->asXML();
    }

    public function getXML(){
		return $this->xml;
    }
}
endif; // class_exists
