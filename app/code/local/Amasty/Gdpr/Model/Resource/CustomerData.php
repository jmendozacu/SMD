<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Resource_CustomerData
{
    public function addCustomerData(Mage_Core_Model_Resource_Db_Collection_Abstract $collection)
    {
        $customerFields = array(
            'email',
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
        );

        foreach ($customerFields as $customerField) {
            $this->joinEAV($collection, $customerField);
        }

        return $this;
    }

    public function joinEAV(
        Mage_Core_Model_Resource_Db_Collection_Abstract $collection,
        $attrCode,
        $mainTable = 'main_table'
    ) {
        $entityType = Mage::getModel('eav/entity_type')->loadByCode('customer');
        $entityTable = $collection->getTable($entityType->getEntityTable());
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('customer', $attrCode);
        $alias = 'table_' . $attrCode;

        if ($attribute->getBackendType() != 'static') {
            $table = $entityTable . '_' . $attribute->getBackendType();
            $attributeId = $attribute->getId();
            $collection->getSelect()
                ->joinLeft(
                    array( $alias => $table),
                    "$mainTable.customer_id = $alias.entity_id AND $alias.attribute_id = $attributeId",
                    array($attribute->getAttributeCode() => $alias . ".value")
                );
        } else {
            $collection->getSelect()
                ->joinLeft(
                    array($alias => $entityTable),
                    "$mainTable.customer_id = $alias.entity_id",
                    $attribute->getAttributeCode()
                );
        }
    }
}
