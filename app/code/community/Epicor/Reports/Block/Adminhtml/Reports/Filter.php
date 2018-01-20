<?php

class Epicor_Reports_Block_Adminhtml_Reports_Filter extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'epicor_reports';
        $this->_controller = 'adminhtml_reports';
        $this->_mode = 'filter';
        $helper = Mage::helper('epicor_reports');
        $this->_headerText = $helper->__('Filter Form');

	    $this->setUseAjax(true);
        $this->removeButton('back');
	    $this->removeButton('save', 'label', 'Filter');
        $this->addButton('filter', array(
            'label' => $helper->__('Create Chart'),
            'class' => 'save',
            'onclick' => "filterForm()"
        ));
        $this->addButton('export', array(
            'label' => $helper->__('Export data to CSV'),
            'class' => 'show-hide',
            'onclick' => "exportData()"
        ));
        $this->addButton('refresh_chart', array(
            'label' => $helper->__('Refresh Chart'),
            'class' => 'task',
            'onclick' => "refreshChart()"
        ));
        $this->addButton('reprocess', array(
            'label' => $helper->__('Process current message log data'),
            'onclick' => "document.location.href = '".$this->getUrl('*/*/reprocess')."'"
        ));
    }

    protected function _prepareLayout()
    {
        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
                ->setTemplate('report/store/switcher.phtml')
        );

        $this->setChild('refresh_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Refresh'),
                    'onclick'   => $this->getRefreshButtonCallback(),
                    'class'   => 'task'
                ))
        );
        parent::_prepareLayout();
        return $this;
    }
}