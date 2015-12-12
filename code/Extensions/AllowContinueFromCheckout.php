<?php namespace Milkyway\SS\Shop\PersistentOrders\Extensions;

/**
 * Milkyway Multimedia
 * AllowContinueController.php
 *
 * @package milkyway-multimedia/ss-shop-persistent-orders
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Extension;
use Member;
use ShoppingCart;

class AllowContinueFromCheckout extends Extension
{
   public function onBeforeInit() {
       if ($order = $this->owner->orderFromSlug($this->owner->Request, 'Action')) {
           if(!$order->MemberID || (Member::currentUserID() && $order->MemberID == Member::currentUserID())) {
               ShoppingCart::singleton()->setCurrent($order);
               $recovered = 'Link';
               $order->extend('onRecover', $recovered);
               return $this->owner->redirect($this->owner->Link());
           }
           else {
               return singleton('Security')->permissionFailure($this->owner, _t('Shop.MUST_BE_LOGGED_IN_TO_CONTINUE_ORDER', 'You must log in to continue your order.'));
           }
       }
   }
}