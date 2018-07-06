<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


$identifier = 'cookie-policy';
foreach (Mage::app()->getStores() as $storeId => $store) {
    if (Mage::getModel('cms/page')->checkIdentifier($identifier, $storeId)) {
        $identifier = 'gdpr-cookie-policy';
        break;
    }
}

$cookiePolicyPage = Mage::getModel('cms/page');
$cookiePolicyPage->setTitle('Cookie Policy');
$cookiePolicyPage->setIdentifier($identifier);
$cookiePolicyPage->setContentHeading('Cookie Policy');
$cookiePolicyPage->setContent('
<p>This site, like many others, uses small files called cookies to help us customize your experience. Find out more about cookies and how you can control them.</p>
<p>What is a cookie?</p>
<p>A cookie is a small file that can be placed on your device that allows us to recognise and remember you. It is sent to your browser and stored on your computer’s hard drive or tablet or mobile device. When you visit our sites, we may collect information from you automatically through cookies or similar technology.</p>
<p>How do we use cookies?</p>
<p>We use cookies in a range of ways to improve your experience on our site, including:</p>
<ul>
<li>Keeping you signed in;</li>
<li>Understanding how you use our site;</li>
<li>Showing you content that is relevant to you;</li>
<li>Showing you products and services that are relevant to you.</li>
</ul>
');
$cookiePolicyPage->setRootTemplate('one_column');
$cookiePolicyPage->setStores(array(0));
$cookiePolicyPage->setMetaKeywords('cookie policy');
$cookiePolicyPage->setIsActive(1);

try {
    $cookiePolicyPage->save();
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'Amasty_Gdpr.log', true);
}

if ('gdpr-cookie-policy' == $identifier) {
    $settingValue = '<p style="margin: 0px 20px;">The Website uses cookies. By continuing to use this website, your consent to our use of these cookies. <a href="/gdpr-cookie-policy" target="_blank">Find out more</a></p>';
    Mage::getConfig()->saveConfig('amgdpr/cookie_policy/notification_text', $settingValue);
}
