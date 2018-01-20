<?php
set_time_limit(0);
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('epicor_common/access_element');

$installer->run("
    INSERT INTO `" . $table . "` 
    (`module`,`controller`,`action`,`block`,`action_type`,`excluded`) 
        VALUES
    ('Mage_Cms','Index','index','','Access',1),
    ('Mage_Cms','Index','defaultIndex','','Access',1),
    ('Mage_Cms','Index','noRoute','','Access',1),
    ('Mage_Cms','Index','defaultNoRoute','','Access',1),
    ('Mage_Cms','Index','noCookies','','Access',1),
    ('Mage_Cms','Index','defaultNoCookies','','Access',1),
    ('Mage_Cms','Page','view','','Access',1),
    ('Mage_Customer','Account','login','','Access',1),
    ('Mage_Customer','Account','loginPost','','Access',1),
    ('Mage_Customer','Account','logout','','Access',1),
    ('Mage_Customer','Account','logoutSuccess','','Access',1),
    ('Mage_Customer','Account','create','','Access',1),
    ('Mage_Customer','Account','createPost','','Access',1),
    ('Mage_Customer','Account','confirm','','Access',1),
    ('Mage_Customer','Account','confirmation','','Access',1),
    ('Mage_Customer','Account','forgotPassword','','Access',1),
    ('Mage_Customer','Account','forgotPasswordPost','','Access',1),
    ('Mage_Customer','Account','resetPassword','','Access',1),
    ('Mage_Customer','Account','resetPasswordPost','','Access',1),
    ('Mage_CatalogSearch','Ajax','suggest','','Access',1),
    ('Mage_CatalogSearch','Term','popular','','Access',1),
    ('Mage_Rss','Catalog','new','','Access',1),
    ('Mage_Rss','Catalog','special','','Access',1),
    ('Mage_Rss','Catalog','salesrule','','Access',1),
    ('Mage_Rss','Catalog','tag','','Access',1),
    ('Mage_Rss','Catalog','notifystock','','Access',1),
    ('Mage_Rss','Catalog','review','','Access',1),
    ('Mage_Rss','Catalog','category','','Access',1),
    ('Mage_Rss','Index','index','','Access',1),
    ('Mage_Rss','Index','nofeed','','Access',1),
    ('Mage_Rss','Index','wishlist','','Access',1),
    ('Mage_Rss','Order','new','','Access',1),
    ('Mage_Rss','Order','customer','','Access',1),
    ('Mage_Rss','Order','status','','Access',1),
    ('Mage_ProductAlert','Add','testObserver','','Access',1),
    ('Mage_ProductAlert','Add','price','','Access',1),
    ('Mage_ProductAlert','Add','stock','','Access',1),
    ('Mage_ProductAlert','Unsubscribe','price','','Access',1),
    ('Mage_ProductAlert','Unsubscribe','priceAll','','Access',1),
    ('Mage_ProductAlert','Unsubscribe','stock','','Access',1),
    ('Mage_ProductAlert','Unsubscribe','stockAll','','Access',1),
    ('Mage_Api','Index','index','','Access',1),
    ('Mage_Api','Soap','index','','Access',1),
    ('Mage_Api','Xmlrpc','index','','Access',1),
    ('Mage_Oauth','Authorize','index','','Access',1),
    ('Mage_Oauth','Authorize','simple','','Access',1),
    ('Mage_Persistent','Index','saveMethod','','Access',1),
    ('Mage_Persistent','Index','expressCheckout','','Access',1),
    ('Mage_XmlConnect','Cart','index','','Access',1),
    ('Mage_XmlConnect','Cart','update','','Access',1),
    ('Mage_XmlConnect','Cart','add','','Access',1),
    ('Mage_XmlConnect','Cart','delete','','Access',1),
    ('Mage_XmlConnect','Cart','coupon','','Access',1),
    ('Mage_XmlConnect','Cart','addGiftcard','','Access',1),
    ('Mage_XmlConnect','Cart','removeGiftcard','','Access',1),
    ('Mage_XmlConnect','Cart','removeStoreCredit','','Access',1),
    ('Mage_XmlConnect','Cart','info','','Access',1),
    ('Mage_XmlConnect','Cart','shoppingCart','','Access',1),
    ('Mage_XmlConnect','Cart','configure','','Access',1),
    ('Mage_XmlConnect','Cart','updateItemOptions','','Access',1),
    ('Mage_XmlConnect','Catalog','category','','Access',1),
    ('Mage_XmlConnect','Catalog','categoryDetails','','Access',1),
    ('Mage_XmlConnect','Catalog','filters','','Access',1),
    ('Mage_XmlConnect','Catalog','product','','Access',1),
    ('Mage_XmlConnect','Catalog','productView','','Access',1),
    ('Mage_XmlConnect','Catalog','productOptions','','Access',1),
    ('Mage_XmlConnect','Catalog','productGallery','','Access',1),
    ('Mage_XmlConnect','Catalog','productReviews','','Access',1),
    ('Mage_XmlConnect','Catalog','productReview','','Access',1),
    ('Mage_XmlConnect','Catalog','search','','Access',1),
    ('Mage_XmlConnect','Catalog','searchDetails','','Access',1),
    ('Mage_XmlConnect','Catalog','searchSuggest','','Access',1),
    ('Mage_XmlConnect','Catalog','sendEmail','','Access',1),
    ('Mage_XmlConnect','Checkout','index','','Access',1),
    ('Mage_XmlConnect','Checkout','newBillingAddressForm','','Access',1),
    ('Mage_XmlConnect','Checkout','newShippingAddressForm','','Access',1),
    ('Mage_XmlConnect','Checkout','billingAddress','','Access',1),
    ('Mage_XmlConnect','Checkout','saveBillingAddress','','Access',1),
    ('Mage_XmlConnect','Checkout','shippingAddress','','Access',1),
    ('Mage_XmlConnect','Checkout','saveShippingAddress','','Access',1),
    ('Mage_XmlConnect','Checkout','shippingMethods','','Access',1),
    ('Mage_XmlConnect','Checkout','shippingMethodsList','','Access',1),
    ('Mage_XmlConnect','Checkout','saveShippingMethod','','Access',1),
    ('Mage_XmlConnect','Checkout','saveMethod','','Access',1),
    ('Mage_XmlConnect','Checkout','paymentMethodList','','Access',1),
    ('Mage_XmlConnect','Checkout','paymentMethods','','Access',1),
    ('Mage_XmlConnect','Checkout','savePayment','','Access',1),
    ('Mage_XmlConnect','Checkout','orderReview','','Access',1),
    ('Mage_XmlConnect','Checkout','orderSummary','','Access',1),
    ('Mage_XmlConnect','Checkout','saveOrder','','Access',1),
    ('Mage_XmlConnect','Checkout','addressMassaction','','Access',1),
    ('Mage_XmlConnect','Checkout','saveAddressInfo','','Access',1),
    ('Mage_XmlConnect','Cms','page','','Access',1),
    ('Mage_XmlConnect','Cms','sentinelSecure','','Access',1),
    ('Mage_XmlConnect','Configuration','index','','Access',1),
    ('Mage_XmlConnect','Customer','login','','Access',1),
    ('Mage_XmlConnect','Customer','logout','','Access',1),
    ('Mage_XmlConnect','Customer','form','','Access',1),
    ('Mage_XmlConnect','Customer','edit','','Access',1),
    ('Mage_XmlConnect','Customer','save','','Access',1),
    ('Mage_XmlConnect','Customer','forgotPassword','','Access',1),
    ('Mage_XmlConnect','Customer','address','','Access',1),
    ('Mage_XmlConnect','Customer','addressForm','','Access',1),
    ('Mage_XmlConnect','Customer','deleteAddress','','Access',1),
    ('Mage_XmlConnect','Customer','saveAddress','','Access',1),
    ('Mage_XmlConnect','Customer','orderList','','Access',1),
    ('Mage_XmlConnect','Customer','orderDetails','','Access',1),
    ('Mage_XmlConnect','Customer','isLoggined','','Access',1),
    ('Mage_XmlConnect','Customer','storeCredit','','Access',1),
    ('Mage_XmlConnect','Customer','giftcardCheck','','Access',1),
    ('Mage_XmlConnect','Customer','giftcardRedeem','','Access',1),
    ('Mage_XmlConnect','Customer','downloads','','Access',1),
    ('Mage_XmlConnect','Customer','checkoutRegistration','','Access',1),
    ('Mage_XmlConnect','Homebanners','index','','Access',1),
    ('Mage_XmlConnect','Index','index','','Access',1),
    ('Mage_XmlConnect','Localization','index','','Access',1),
    ('Mage_XmlConnect','OfflineCatalog','index','','Access',1),
    ('Mage_XmlConnect','Mecl','start','','Access',1),
    ('Mage_XmlConnect','Mecl','return','','Access',1),
    ('Mage_XmlConnect','Mecl','review','','Access',1),
    ('Mage_XmlConnect','Mecl','orderReview','','Access',1),
    ('Mage_XmlConnect','Mecl','shippingMethods','','Access',1),
    ('Mage_XmlConnect','Mecl','saveShippingMethod','','Access',1),
    ('Mage_XmlConnect','Mecl','placeOrder','','Access',1),
    ('Mage_XmlConnect','Mecl','cancel','','Access',1),
    ('Mage_XmlConnect','Mep','index','','Access',1),
    ('Mage_XmlConnect','Mep','saveShippingAddress','','Access',1),
    ('Mage_XmlConnect','Mep','shippingMethods','','Access',1),
    ('Mage_XmlConnect','Mep','saveShippingMethod','','Access',1),
    ('Mage_XmlConnect','Mep','cartTotals','','Access',1),
    ('Mage_XmlConnect','Mep','saveOrder','','Access',1),
    ('Mage_XmlConnect','Pbridge','index','','Access',1),
    ('Mage_XmlConnect','Pbridge','result','','Access',1),
    ('Mage_XmlConnect','Pbridge','output','','Access',1),
    ('Mage_XmlConnect','Review','form','','Access',1),
    ('Mage_XmlConnect','Review','save','','Access',1),
    ('Mage_XmlConnect','Wishlist','index','','Access',1),
    ('Mage_XmlConnect','Wishlist','details','','Access',1),
    ('Mage_XmlConnect','Wishlist','add','','Access',1),
    ('Mage_XmlConnect','Wishlist','remove','','Access',1),
    ('Mage_XmlConnect','Wishlist','clear','','Access',1),
    ('Mage_XmlConnect','Wishlist','update','','Access',1),
    ('Mage_XmlConnect','Wishlist','cart','','Access',1),
    ('Epicor_Common','Account','error','','Access',1),
    ('Epicor_Common','Account','login','','Access',1),
    ('Epicor_Common','Account','loginPost','','Access',1),
    ('Epicor_Common','Account','logout','','Access',1),
    ('Epicor_Common','Account','logoutSuccess','','Access',1),
    ('Epicor_Common','Account','create','','Access',1),
    ('Epicor_Common','Account','createPost','','Access',1),
    ('Epicor_Common','Account','confirm','','Access',1),
    ('Epicor_Common','Account','confirmation','','Access',1),
    ('Epicor_Common','Account','forgotPassword','','Access',1),
    ('Epicor_Common','Account','forgotPasswordPost','','Access',1),
    ('Epicor_Common','Account','resetPassword','','Access',1),
    ('Epicor_Common','Account','resetPasswordPost','','Access',1),
    ('Epicor_Common','Account','edit','','Access',1),
    ('Epicor_Common','Account','editPost','','Access',1);
");

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$helper->regenerateModuleElements('Mage_Cms');
$helper->regenerateModuleElements('Mage_Customer');
$helper->regenerateModuleElements('Mage_CatalogSearch');
$helper->regenerateModuleElements('Mage_Rss');
$helper->regenerateModuleElements('Mage_ProductAlert');
$helper->regenerateModuleElements('Mage_Api');
$helper->regenerateModuleElements('Mage_Oauth');
$helper->regenerateModuleElements('Mage_Persistent');
$helper->regenerateModuleElements('Mage_XmlConnect');
$helper->regenerateModuleElements('Mage_Core');
$helper->regenerateModuleElements('Mage_Directory');
$helper->regenerateModuleElements('Mage_Catalog');
$helper->regenerateModuleElements('Mage_Sales');
$helper->regenerateModuleElements('Mage_Shipping');
$helper->regenerateModuleElements('Mage_Paygate');
$helper->regenerateModuleElements('Mage_Checkout');
$helper->regenerateModuleElements('Mage_Paypal');
$helper->regenerateModuleElements('Mage_Poll');
$helper->regenerateModuleElements('Mage_GoogleCheckout');
$helper->regenerateModuleElements('Mage_Review');
$helper->regenerateModuleElements('Mage_Tag');
$helper->regenerateModuleElements('Mage_Wishlist');
$helper->regenerateModuleElements('Mage_GiftMessage');
$helper->regenerateModuleElements('Mage_Contacts');
$helper->regenerateModuleElements('Mage_Sendfriend');
$helper->regenerateModuleElements('Mage_Authorizenet');
$helper->regenerateModuleElements('Mage_Bundle');
$helper->regenerateModuleElements('Mage_Captcha');
$helper->regenerateModuleElements('Mage_Centinel');
$helper->regenerateModuleElements('Mage_Compiler');
$helper->regenerateModuleElements('Mage_Newsletter');
$helper->regenerateModuleElements('Mage_Downloadable');
$helper->regenerateModuleElements('Epicor_B2b');
$helper->regenerateModuleElements('Epicor_Common');
$helper->regenerateModuleElements('Epicor_Comm');
$helper->regenerateModuleElements('Epicor_ErpSimulator');
$helper->regenerateModuleElements('Epicor_Esdm');
$helper->regenerateModuleElements('Epicor_FlexiTheme');
$helper->regenerateModuleElements('Epicor_ProductFeed');
$helper->regenerateModuleElements('Epicor_QuickOrderPad');
$helper->regenerateModuleElements('Epicor_Quotes');
$helper->regenerateModuleElements('Epicor_Verifone');
$helper->regenerateModuleElements('Phoenix_Moneybookers');

// create default group

$group = $helper->addAccessGroup('Customer - Full Access');

// get all non-excluded elements and include them

$collection = Mage::getModel('epicor_common/access_element')->getCollection();
/* @var $collection Epicor_Common_Model_Resource_Access_Element_Collection */
$collection->addFieldToFilter('excluded', 0);
$right = $helper->addAccessRight('Customer - Full Access');
$helper->addAccessGroupRight($group->getId(), $right->getId());

foreach ($collection->getItems() as $element) {
    /* @var $collection Epicor_Common_Model_Access_Element */
    $helper->addAccessRightElementById($right->getId(), $element->getId());
}

$installer->endSetup();