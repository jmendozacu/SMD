<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_Policy_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('amgdprPolicyGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        /** @var Amasty_Gdpr_Model_Resource_PrivacyPolicy_Collection $collection */
        $collection = Mage::getResourceModel('amgdpr/privacyPolicy_collection');
        $collection->joinAdminUser();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => $this->__('ID'),
                'index' => 'id',
                'width' => '30'
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => $this->__('Date Created'),
                'index' => 'created_at',
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

        $this->addColumn(
            'date_last_edited',
            array(
                'header' => $this->__('Date Last Edited'),
                'index' => 'date_last_edited',
                'type' => 'datetime'
            )
        );

        $this->addColumn(
            'last_edited_by',
            array(
                'header' => $this->__('Last Edited By'),
                'index' => 'last_edited_by',
                'renderer' => 'Amasty_Gdpr_Block_Adminhtml_Policy_Grid_Renderer_Admin',
                'filter_index' => new Zend_Db_Expr('CONCAT(firstname, \' \' ,lastname)')
            )
        );

        $this->addColumn(
            'comment',
            array(
                'header' => $this->__('Comment'),
                'index' => 'comment'
            )
        );

        $this->addColumn(
            'status',
            array(
                'header' => $this->__('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => Mage::getModel('amgdpr/privacyPolicy')->getStatuses()
            )
        );

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId())
        );
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('policy');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => $this->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => $this->__('Are you sure?')
            )
        );

        return $this;
    }
}
