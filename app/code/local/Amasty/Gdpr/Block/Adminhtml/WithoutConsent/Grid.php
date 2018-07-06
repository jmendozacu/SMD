<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_WithoutConsent_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        /** @var Amasty_Gdpr_Model_Resource_ConsentLog_Collection $consentLog */
        $consentLog = Mage::getResourceModel('amgdpr/consentLog_collection');
        $consentLog->getSelect()->group('customer_id');
        $customerIds = $consentLog->getColumnValues('customer_id');

        /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->joinAttribute(
                'billing_country_id',
                'customer_address/country_id',
                'default_billing',
                null,
                'left'
            );

        if ($customerIds) {
            $collection->addFieldToFilter('entity_id', array('nin' => $customerIds));
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            array(
                'header' => $this->__('Name'),
                'index' => 'name',
                'renderer' => 'Amasty_Gdpr_Block_Adminhtml_WithoutConsent_Grid_Renderer_Name',
                'filter' => false,
                'sortable' => false
            )
        );

        $this->addColumn(
            'email',
            array(
                'header' => $this->__('Email'),
                'index' => 'email'
            )
        );

        $this->addColumn('billing_country_id', array(
            'header' => $this->__('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'billing_country_id',
        ));

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('without_consents');

        $this->getMassactionBlock()->addItem('email_consent', array(
            'label' => $this->__('Email Consent Request'),
            'url' => $this->getUrl('*/*/emailConsent'),
            'confirm' => $this->__('Are you sure?')
        ));

        return parent::_prepareMassaction();
    }
}
