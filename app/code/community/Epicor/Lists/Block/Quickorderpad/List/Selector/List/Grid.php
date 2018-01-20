<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Lists_Block_Quickorderpad_List_Selector_List_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();
        
        $listHelper = Mage::helper('epicor_lists/frontend');
        /* @var $listHelper Epicor_Lists_Helper_Frontend */

        $this->setId('quickorderpad_list_selector_list');
        $this->setIdColumn('id');
        $this->setDefaultSort('name');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(false);
        $this->setMessageBase('epicor_comm');
        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setRowClickCallback('populateListsSelect');
        $this->setCacheDisabled(true);
        
        $lists = $listHelper->getQuickOrderPadLists();
        
        $store = Mage::app()->getStore();
        foreach ($lists as $list) {
            /* @var $list Epicor_Lists_Model_List */
            $label = $list->getStoreLabel($store);
            $list->setLabel($label);
        }
        
        $this->setCustomData($lists);
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml(false);
        return $html;
    }

    protected function _getColumns()
    {
        $columns = array(
            'title' => array(
                'header' => Mage::helper('epicor_lists')->__('Title'),
                'align' => 'left',
                'index' => 'title',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'type' => array(
                'header' => Mage::helper('epicor_lists')->__('Type'),
                'align' => 'center',
                'index' => 'type',
                'type' => 'options',
                'width' => 150,
                'options' => Mage::getModel('epicor_lists/list_type')->toQopFilterArray()
            ),
            'product_count' => array(
                'header' => Mage::helper('epicor_lists')->__('Product Count'),
                'align' => 'center',
                'index' => 'product_count',
                'type' => 'number',
                'width' => 50,
            ), 
            'entity_id' => array(
                'header' => Mage::helper('epicor_lists')->__('id'),
                'align' => 'left',
                'index' => 'id',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'type' => 'hidden',
                'condition' => 'LIKE'
            ),
        );
        
        return $columns;
    }

    public function getRowUrl($row)
    {
        return $row->getId();
    }

}
