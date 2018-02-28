<?php
/**
 * Created by PhpStorm.
 * User: Bobesz
 * Date: 2/28/2018
 * Time: 1:35 AM
 */

class collector
{
    private $orders = array();

    public function __construct(){

    }

    public function collectOrders(){

        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=shop', 'shop', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        }
        catch (PDOException $e)
        {
            echo 'Error: ' . $e->getMessage();
            exit();
        }

        //$sql = 'SELECT * FROM bobnethu_woocommerce_tax_rates';
        //$tax_rates = $pdo->prepare($sql);

        $sql = 'SELECT DISTINCT order_id FROM bobnethu_woocommerce_order_items';
        $orderids = $pdo->prepare($sql);
        $orderids->execute();
        while ($orderid = $orderids->fetch())
        {
            //order
            $order = new order();
            $order->orderID = $orderid['order_id'];

            $sql = 'SELECT order_item_id FROM bobnethu_woocommerce_order_items WHERE order_id = '.$order->orderID;
            $orderitemids =  $pdo->prepare($sql);
            $orderitemids->execute();

            //order items
            while ($orderitemid = $orderitemids->fetch())
            {
                $item = new item();

                $sql = 'SELECT * FROM bobnethu_woocommerce_order_itemmeta WHERE order_item_id = '.$orderitemid['order_item_id'].' AND (meta_key in (\'_product_id\', \'_line_subtotal\', \'_line_subtotal_tax\'))';
                $itemmeta = $pdo->prepare($sql);
                $itemmeta->execute();

                while ($row = $itemmeta->fetch())
                {
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
                    }

                }

                $sql = 'SELECT post_title FROM bobnethu_posts WHERE ID = '.$item->itemID;
                $itemname = $pdo->prepare($sql);
                $itemname->execute();
                $name = $itemname->fetch();
                $item->name = $name['post_title'];
                $order->addItem($item);
            }

            $sql = 'SELECT post_date FROM bobnethu_posts WHERE ID = '.$order->orderID;
            $orderdate = $pdo->prepare($sql);
            $orderdate->execute();
            $date = $orderdate->fetch();
            $order->date = $date['post_date'];

            //customer, other
            $sql = 'SELECT * FROM shop.bobnethu_postmeta WHERE post_id = '.$order->orderID;
            $ordermeta = $pdo->prepare($sql);
            $ordermeta->execute();

            //$address = new address();
            //$customer = new customer();

            $shipping_customer = new customer();
            $billing_customer = new customer();

            $shipping_address = new address();
            $billing_address = new address();

            while ($meta = $ordermeta->fetch())
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

}