<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_ActionLog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        /** @var Amasty_Gdpr_Model_Resource_ActionLog_Collection $collection */
        $collection = Mage::getResourceModel('amgdpr/actionLog_collection');
        Mage::getResourceModel('amgdpr/customerData')->addCustomerData($collection);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'customer_id',
            array(
                'header' => $this->__('Customer ID'),
                'index' => 'customer_id',
            )
        );

        $this->addColumn(
            'customer_name',
            array(
                'header' => $this->__('Customer'),
                'index' => 'customer_id',
                'renderer' => 'Amasty_Gdpr_Block_Adminhtml_Consent_Grid_Renderer_Name',
                'filter' => false,
                'sortable' => false
            )
        );

        $this->addColumn(
            'ip',
            array(
                'header' => $this->__('IP Address'),
                'index' => 'ip'
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => $this->__('Date'),
                'index' => 'created_at',
                'type' => 'datetime',
                'filter_index' => 'main_table.created_at'
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => $this->__('Action'),
                'index' => 'action',
                'type' => 'options',
                'options' => Mage::getSingleton('amgdpr/actionLog')->getOptions()
            )
        );

        return parent::_prepareColumns();
    }
}
