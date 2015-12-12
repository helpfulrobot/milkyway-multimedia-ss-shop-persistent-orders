<?php namespace Milkyway\SS\Shop\PersistentOrders;

/**
 * Milkyway Multimedia
 * SetCart.php
 *
 * @package milkyway-multimedia/ss-shop-persistent-orders
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use RequestFilter;

use SS_HTTPRequest as Request;
use SS_HTTPResponse as Response;
use Session;
use DataModel;
use Cookie;
use Member;
use DB;

use ShoppingCart;
use Order;

class SetCart implements RequestFilter
{
    protected $done = false;

    public function preRequest(Request $request, Session $session, DataModel $model)
    {
        if($this->done) {
            return;
        }

        if (!DB::get_conn()) {
            global $databaseConfig;
            if ($databaseConfig) {
                DB::connect($databaseConfig);
            }
        }

        $cart = ShoppingCart::curr();
        $recovered = '';
        $idVar = singleton('env')->get('ShopConfig.cookie_id', 'shopping_cart_id');

        $filters = [
            'Status' => 'Cart',
        ];

        if(!Member::currentUserID() && Member::config()->login_joins_cart) {
            $filters['MemberID'] = 0;
        }

        if (!$cart &&
            ($id = Cookie::get($idVar)) &&
            $order = Order::get()->filter(array_merge($filters, [
                'PersistenceReference' => $id,
            ]))->first()) {
            ShoppingCart::singleton()->setCurrent($order);
            $recovered = 'Cookie';
        }
        else if(!$cart &&
            Member::currentUserID() &&
            singleton('env')->get('ShopConfig.get_last_cart_of_member', true) &&
            $order = Order::get()->filter(array_merge($filters, [
                'MemberID' => Member::currentUserID(),
            ]))->sort('LastEdited DESC')->first()) {
            ShoppingCart::singleton()->setCurrent($order);
            $recovered = 'Last Member Order';
        }

        if($cart && !$cart->PersistenceReference) {
            $cart->generateCartId();
        }
        else if($cart && !Cookie::get($idVar)) {
            Cookie::set($idVar, $cart->PersistenceReference, singleton('env')->get('ShopConfig.persist_order_days', 90));
        }

        if($recovered && $cart) {
            $cart->extend('onRecovered', $recovered);
        }

        $this->done = true;
    }

    public function postRequest(Request $request, Response $response, DataModel $model)
    {

    }
}