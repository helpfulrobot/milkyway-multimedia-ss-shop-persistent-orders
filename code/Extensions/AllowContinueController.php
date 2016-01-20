<?php namespace Milkyway\SS\Shop\PersistentOrders\Extensions;

/**
 * Milkyway Multimedia
 * AllowContinueController.php
 *
 * @package milkyway-multimedia/ss-shop-persistent-orders
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use SS_HTTPRequest as Request;
use SS_HTTPResponse_Exception as ResponseException;
use ShoppingCart;
use CartPage;
use CheckoutPage;
use OrderManipulation;
use Order;
use Convert;

class AllowContinueController extends OrderManipulation
{
    private static $allowed_actions = [
        'order',
        'ActionsForm',
    ];

    public function order(Request $request)
    {
        $response = null;

        try {
            $response = parent::order($request);
        } catch (ResponseException $error) {
            if ($error->getResponse()->getStatusCode() != 404 || !$this->continueOrder($request)) {
                throw $error;
            }
        }

        return $response;
    }

    public function orderFromSlug($request, $param = 'ID')
    {
        $slug = Convert::raw2sql($request->param($param));

        if (!$slug) {
            return $slug;
        }

        $filters = [
            'Slug' => $slug,
        ];

        $this->owner->extend('updateOrderFromSlug', $filters);

        return Order::get()->filter($filters)->first();
    }

    protected function continueOrder($request)
    {
        if (($order = $this->owner->orderFromSlug($request)) && $order->IsCart()) {
            ShoppingCart::singleton()->setCurrent($order);
            $recovered = 'Link';
            $order->extend('onRecover', $recovered);

            if ($checkoutLink = CheckoutPage::find_link()) {
                $this->owner->redirect($checkoutLink);

                return true;
            } else {
                if ($cartLink = CartPage::find_link()) {
                    $this->owner->redirect($cartLink);

                    return true;
                }
            }
        }

        return false;
    }
}
