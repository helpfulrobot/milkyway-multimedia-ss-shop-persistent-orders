<?php namespace Milkyway\SS\Shop\PersistentOrders\Extensions;

/**
 * Milkyway Multimedia
 * Persist.php
 *
 * @package milkyway-multimedia/ss-shop-persistent-orders
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use DataExtension;
use Object;
use Cookie;

class Persist extends DataExtension
{
    private static $db = [
        'PersistenceReference' => 'Varchar(64)',
    ];

    public function onStartOrder() {
        $this->generateCartId();
    }

    public function generateCartId() {
        if($this->owner->PersistenceReference) {
            return;
        }

        $generator = Object::create('Milkyway\SS\Shop\PersistentOrders\HashGenerator');
        $list = $this->owner->get();

        if($this->owner->ID) {
            $list = $list->exclude('ID', $this->owner->ID);
        }

        while(!$this->owner->PersistenceReference || $list->filter('PersistenceReference', $this->owner->PersistenceReference)->exists()) {
            $this->owner->PersistenceReference = substr($generator->randomToken(), 0, 64);
        }

        Cookie::set(singleton('env')->get('ShopConfig.cookie_id', 'shopping_cart_id'), $this->owner->PersistenceReference, singleton('env')->get('ShopConfig.persist_order_days', 90));

        if($this->owner->exists()) {
            $this->owner->write();
        }
    }

    public function onPlaceOrder() {
        Cookie::force_expiry(singleton('env')->get('ShopConfig.cookie_id', 'shopping_cart_id'));
    }
}