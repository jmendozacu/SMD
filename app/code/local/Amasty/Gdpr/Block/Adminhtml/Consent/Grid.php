<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_Consent_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        /** @var Amasty_Gdpr_Model_Resource_ConsentLog_Collection $collection */
        $collection = Mage::getResourceModel('amgdpr/consentLog_collection');
        Mage::getResourceModel('amgdpr/customerData')->addCustomerData($collection);
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
                'renderer' => 'Amasty_Gdpr_Block_Adminhtml_Consent_Grid_Renderer_Name',
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

        $this->addColumn(
            'date_consented',
            array(
                'header' => $this->__('Date Consented'),
                'index' => 'date_consented',
                'type' => 'datetime'
            )
        );

        $this->addColumn(
            'policy_version',
            array(
                'header' => $this->__('Policy Version'),
                'index' => 'policy_version'
            )
        );

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('consents');

        $this->getMassactionBlock()->addItem('email_consent', array(
            'label' => $this->__('Email Consent Request'),
            'url' => $this->getUrl('*/*/emailConsent'),
            'confirm' => $this->__('Are you sure?')
        ));

        return parent::_prepareMassaction();
    }
}
