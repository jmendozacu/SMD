<?php

/**
 * 1.0.7.0.0-1.0.7.2.0
 *
 * Converts single ewa configurator values to a multiselect field
 */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$collection = Mage::getModel('core/config_data')->getCollection();
/* @var $collection Mage_Core_Model_Resource_Config_Data_Collection */

$collection->addPathFilter('Epicor_Comm/ewa_options');

$fields = array(
    'base_description',
    'ewa_title',
    'ewa_sku',
    'ewa_short_description',
    'ewa_description',
);

$data = array();
foreach ($collection->getItems() as $item) {
    $scope = $item->getScope();
    $scopeId = $item->getScopeId();

    if (!isset($data[$scope][$scopeId])) {
        $data[$scope][$scopeId] = array();//array_fill_keys($fields, 1);
    }

    $sections = explode('/', $item->getPath());
    $field = array_pop($sections);

    if (in_array($field, $fields)) {
        $data[$scope][$scopeId][$field] = $item->getValue();
    }
}

foreach ($data as $scope => $scopeData) {
    foreach ($scopeData as $scopeId => $sectionData) {
        foreach ($fields as $field) {
            if (!isset($sectionData[$field])) {
                if ($scope == 'stores') {
                    $websiteId = Mage::app()->getStore($scopeId)->getWebsiteId();
                    if (isset($data['websites'][$websiteId][$field])) {
                        $value = $data['websites'][$websiteId][$field];
                    } elseif (isset($data['default'][0][$field])) {
                        $value = $data['default'][0][$field];
                    } else {
                        $value = '1';
                    }
                } elseif ($scope == 'websites') {
                    if (isset($data['default'][0][$field])) {
                        $value = $data['default'][0][$field];
                    } else {
                        $value = '1';
                    }
                } else {
                    $value = '1';
                }

                $data[$scope][$scopeId][$field] = $value;
            }
        }
    }
}

foreach ($data as $scope => $scopeData) {
    foreach ($scopeData as $scopeId => $sectionData) {
        $fields = array_keys($sectionData, '1');
        $value = join(',', $fields);
        $configData = Mage::getModel('core/config_data');
        $configData->setScope($scope);
        $configData->setScopeId($scopeId);
        $configData->setPath('Epicor_Comm/ewa_options/ewa_display');
        $configData->setValue($value);
        $configData->save();
    }
}

$installer->endSetup();
