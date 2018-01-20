<?php

$site = Mage::getModel('hostingmanager/site');
/* @var $site Epicor_HostingManager_Model_Site */

$site->load(true, 'is_default');
if (!$site->getId()) {

    $url = parse_url(Mage::getStoreConfig('web/unsecure/base_url', 0), PHP_URL_HOST);

    $site->setName('Default Website');
    $site->setUrl($url);
    $site->setIsWebsite(false);
    $site->setCode('');
    $site->setChildId(0);
    $site->setIsDefault(true);
    $site->save();
}