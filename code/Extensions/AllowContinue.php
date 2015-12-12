<?php namespace Milkyway\SS\Shop\PersistentOrders\Extensions;

/**
 * Milkyway Multimedia
 * AllowContinue.php
 *
 * @package milkyway-multimedia/ss-shop-persistent-orders
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\Behaviours\Extensions\Sluggable;
use CheckoutPage;

class AllowContinue extends Sluggable
{
    public function onBeforeWrite()
    {
        // Clear hash when order is placed, meaning it can no longer be continued
        if ($this->owner->Placed) {
            $this->owner->Slug = null;
        }
        else {
            parent::onBeforeWrite();
        }
    }

    public function getContinueLink()
    {
        if($this->owner->IsCart() && $this->owner->Slug && $checkout = CheckoutPage::get()->first()) {
            if($checkout->hasExtension('Milkyway\SS\Shop\PersistentOrders\Extensions\AllowContinueFromCheckout')) {
                return $checkout->Link($this->owner->Slug);
            }
            else {
                return $checkout->Link('order/' . $this->owner->Slug);
            }
        }

        return '';
    }
}