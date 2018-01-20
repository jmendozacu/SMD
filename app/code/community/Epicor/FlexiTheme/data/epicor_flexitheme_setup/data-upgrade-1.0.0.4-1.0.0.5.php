<?php

/**
 * Version 1.0.0.4 to 1.0.0.5 upgrade
 * 
 * And adds currency block into flexitheme
 */
$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */
/* * **********************************************************************
  Step : Populate Layout Blocks
 * *********************************************************************** */


if ($helper->isModuleEnabled('Epicor_FlexiTheme')) {   
    if (!$helper->findLayoutBlock('flexitheme/frontend_template_currency')) {
        $helper->createLayoutBlock('Currency', 'flexitheme/frontend_template_currency', 'page/template/currency.phtml', NULL, NULL, NULL, NULL, 'epicor_currency', NULL);
    }
}

