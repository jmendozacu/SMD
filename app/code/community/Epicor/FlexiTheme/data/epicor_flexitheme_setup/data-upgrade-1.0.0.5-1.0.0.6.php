<?php //

/**
 * Version 1.0.0.6 to 1.0.0.7 upgrade
 * 
 * And adds flexitheme skins and layouts into flexitheme
 */
set_time_limit(0);

$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */

$helper->importLayout('Epicor');

$helper->importTheme('Flexi Theme 1');
$helper->importTheme('Flexi Theme 2');
$helper->importTheme('Flexi Theme 3');
$helper->importTheme('Epicor');
//
$mage_config = new Mage_Core_Model_Config();
$mage_config->saveConfig('design/package/name', 'rwd');
$mage_config->saveConfig('design/theme/templates', 'default');
//
//$layoutHelper = Mage::helper('flexitheme/layout');
///* @var $helper Epicor_Flexitheme_Helper_Layout */
//$layout = Mage::getModel('flexitheme/layout')->load('Epicor', 'layout_name');
///* @var $layout Epicor_Flexitheme_Model_Layout */
//
//$layoutHelper->setActiveLayout($layout->getId());
//
$mage_config->saveConfig('design/theme/layout', 'default');
//
//$themeHelper = Mage::helper('flexitheme/theme');
///* @var $helper Epicor_Flexitheme_Helper_Theme */
//$theme = Mage::getModel('flexitheme/theme')->load('Epicor', 'theme_name');
///* @var $theme Epicor_Flexitheme_Model_Theme */
//
//$themeHelper->setActiveLayout($theme->getId());
//
//$mage_config->saveConfig('design/theme/skin', $layoutHelper->safeString($theme->getThemeName()));
//
//$skin = $layoutHelper->safeString($layoutOrSkin);
//$skinLogoLocation = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_SKIN) . DS . 'frontend' . DS . 'flexitheme' . DS . $skin . DS . 'images' . DS . 'logo_email.gif';
//if (file_exists($skinLogoLocation)) {
//    $newLogoLocation = Mage::getBaseDir() . DS . 'media' . DS . 'email' . DS . 'logo' . DS . 'stores' . DS . 1;
//    if (!is_dir($newLogoLocation)) {
//        mkdir($newLogoLocation, 755, true);
//    }
//    
//    $mage_config->saveConfig('design/email/logo', 'stores/1/logo_email.gif');
//    copy($skinLogoLocation, $newLogoLocation . DS . 'logo_email.gif');
//}
