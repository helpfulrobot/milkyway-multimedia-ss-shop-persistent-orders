Silverstripe Shop - Persistent Orders
======
**Persistent Orders** allows orders to be stored via cookies, so users can continue them later. It also allows you to continue orders from a link, so you can send them a continue link via email

## Install
Add the following to your composer.json file
```

    "require"          : {
		"milkyway-multimedia/ss-shop-persistent-orders": "~0.1"
	}
	
```

### Important: Extensions are not added automatically
You will have to add the ones you want

**Order Extensions**
- Milkyway\SS\Shop\PersistentOrders\Extensions\Persist - Order will persist in a cookie, and will be retrieved as a last resort from session etc.
- Milkyway\SS\Shop\PersistentOrders\Extensions\AllowContinue - Allow shopping carts to be continued via a url
- Milkyway\SS\Shop\PersistentOrders\Extensions\AllowContinueFromCheckout - This will change the continue link from checkout/order/$Slug to checkout/$Slug

## License 
* MIT

## Version 
* Version 0.1 (Alpha)

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")