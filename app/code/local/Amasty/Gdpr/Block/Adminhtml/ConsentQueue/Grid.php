<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_ConsentQueue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        /** @var Amasty_Gdpr_Model_Resource_ConsentLog_Collection $collection */
        $collection = Mage::getResourceModel('amgdpr/consentQueue_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'customer_id',
            array(
                'header' => $this->__('Customer ID'),
                'index' => 'customer_id'
            )
        );

        $this->addColumn('status', array(
            'header' => $this->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('amgdpr/consentQueue')->getAvailableStatuses()
        ));

        return parent::_prepareColumns();
    }
}
