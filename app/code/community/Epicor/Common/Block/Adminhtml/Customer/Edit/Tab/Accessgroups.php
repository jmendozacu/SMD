<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Epicor_Common_Block_Adminhtml_Customer_Edit_Tab_Accessgroups extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    private $_selected = array();
    
    public function __construct() {
        parent::__construct();
        $this->setId('customer_access_groups');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('in_group' => 1));
    }

    public function getTabUrl() {
        return $this->getUrl('adminhtml/epicorcommon_customer_access/group', array('_current' => true));
    }

    public function getTabClass() {
        return 'ajax';
    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_group') {
            $groupIds = $this->_getSelected();
            if (empty($groupIds)) {
                $groupIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $groupIds));
            } elseif (!empty($groupIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $groupIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('epicor_common/access_group_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('in_group', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'groups[]',
            'name' => 'group',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'editable' => true,
            'index' => 'entity_id'
        ));

        $this->addColumn('group', array(
            'header' => Mage::helper('customer')->__('Access Group'),
            'index' => 'entity_name'
        ));
        
        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        $customer = Mage::registry('current_customer');
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/groupgrid', array('id' => $customer->getId(), '_current' => true));
    }

    /**
     * Sets the currently selected items
     * 
     * @param array $selected
     */
    public function setSelected($selected) {
        if(!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }
    
    public function _getSelected() {
        return array_keys($this->getSelected());
    }

    public function getSelected() {
        
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $this->_selected = array();
            $collection = Mage::getModel('epicor_common/access_group_customer')->getCollection();
        
            $customer = Mage::registry('current_customer');
            
            $collection->addFieldToFilter('customer_id', $customer->getId());

            foreach ($collection->getItems() as $customer) {
                $this->_selected[$customer->getGroupId()] = array('id' => $customer->getGroupId());
            }
        }
        
        return $this->_selected;
    }

    public function getTabLabel() {
        return Mage::helper('epicor_common')->__('Access Groups');
    }

    public function getTabTitle() {
        return Mage::helper('epicor_common')->__('Access Groups');
    }

    public function isHidden() {
        return false;
    }

    public function canShowTab() {
        return true;
    }

}
