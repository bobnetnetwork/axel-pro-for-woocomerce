<?php
/**
 * Created by PhpStorm.
 * User: bobes
 * Date: 2/28/2018
 * Time: 1:29 AM
 */

class axelProXML
{
    private $orders;

    public function __construct($orders){
        $this->orders = $orders;
    }

    public function generateXML()
    {
        //UTF-8 XML fejléc küldése a böngészőnek
        header("Content-type: text/xml; charset=utf-8");
        //XML fejléc kiírása
        print("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>");
        print("<AXELPRO_IMP_TRANS VERSION=\"1.1\">");

        foreach ($this->orders as &$order) {
            //ide kell egy iff ha nem üres
            //XML ügylet elejének kiírása
            print("<TRANS>");
            //XML ügylet adatai elejének kiírása
            print("<TRANS_HEAD>");
            //IMG_TYPE = 3: bejövő megrendelés
            print("<IMG_TYPE>3</IMG_TYPE>");
            print("<IMG_DATETIME>{$order->date}</IMG_DATETIME>");
            print("<IMG_FULFILMENT_DATE>{$order->date}</IMG_FULFILMENT_DATE>");
            print("<IMG_DEADLINE_DATE>{$order->date}</IMG_DEADLINE_DATE>");
            print("<IMG_CUSTOMER_NAME>{$order->billing_customer->lastname} {$order->billing_customer->firstname}</IMG_CUSTOMER_NAME>");
            print("<IMG_CUSTOMER_ADDRESS>{$order->billing_customer->address->postcode} {$order->billing_customer->address->city}, {$order->billing_customer->address->address1} {$order->billing_customer->address->address2}</IMG_CUSTOMER_ADDRESS>");
            print("<IMG_CUSTOMER_OTHER></IMG_CUSTOMER_OTHER>");
            print("<IMG_POST_NAME>{$order->shipping_customer->lastname} {$order->shipping_customer->firstname}</IMG_POST_NAME>");
            print("<IMG_POST_ADDRESS>{$order->shipping_customer->address->postcode} {$order->shipping_customer->address->city}, {$order->shipping_customer->address->address1} {$order->shipping_customer->address->address2}</IMG_POST_ADDRESS>");
            print("<IMG_PAY_NAME>{$order->payment_method}</IMG_PAY_NAME>");
            print("<IMG_CURR>{$order->currency}</IMG_CURR>");
            print("<IMG_RATE>1</IMG_RATE>");
            print("<IMG_PRICE_TYPE>1</IMG_PRICE_TYPE>");
            print("<IMG_DISCOUNT>0</IMG_DISCOUNT>");
            print("<IMG_COPIES>2</IMG_COPIES>");
            print("<IMG_COMMENT></IMG_COMMENT>");
            print("<IMG_IS_MOVE>1</IMG_IS_MOVE>");
            print("<IMG_IS_PAID>1</IMG_IS_PAID>");
            print("<IMG_LANG>0</IMG_LANG>");
            print("<IMG_OTHER></IMG_OTHER>");
            print("<IMG_STORNO>0</IMG_STORNO>");
            print("<IMG_IS_ADVANCE>0</IMG_IS_ADVANCE>");
            print("<IMG_IS_CORRECTION>0</IMG_IS_CORRECTION>");
            print("<IMG_ENVELOPE>0</IMG_ENVELOPE>");
            print("<IMG_COMPANY_PLUS>1</IMG_COMPANY_PLUS>");

            //XML ügylet adatai végének kiírása
            print("</TRANS_HEAD>");

            //XML ügylet tételek elejének kiírása
            print("<TRANS_ITEMS>");
            $items = $order->items;
            $i = 1;
            foreach ($items as &$item) {
                //XML ügylet tétel elejének kiírása
                print("<TRANS_ITEM>");

                print("<ITM_NAME>{$item->name}</ITM_NAME>");
                print("<ITM_PRICE_PRICE>{$item->value}</ITM_PRICE_PRICE>");
                print("<ITM_PRICE_DISCOUNT>{$item->value}</ITM_PRICE_DISCOUNT>");
                print("<ITM_PRICE_ORIG>{$item->value}</ITM_PRICE_ORIG>");
                print("<ITM_PRICE_VAT_SHORT>27%</ITM_PRICE_VAT_SHORT>");
                print("<ITM_DATETIME>{$order->date}</ITM_DATETIME>");
                print("<ITM_AMOUNT>1</ITM_AMOUNT>");
                print("<ITM_UNIT>db</ITM_UNIT>");
                print("<ITM_VTSZSZJ></ITM_VTSZSZJ>");
                print("<ITM_ORD>{$i}</ITM_ORD>");//sorszám a bizonylaton
                print("<ITM_COMMENT></ITM_COMMENT>");

                //XML ügylet tétel végének kiírása
                print("</TRANS_ITEM>");
                ++$i;
            }
            //XML ügylet tételek végének kiírása
            print("</TRANS_ITEMS>");

            //XML ügylet végének kiírása
            print("</TRANS>");
        }
        //XML végének kiírása
        print("</AXELPRO_IMP_TRANS>");
        //kimeneti buffer ürítése a böngésző felé
        flush();
    }

}