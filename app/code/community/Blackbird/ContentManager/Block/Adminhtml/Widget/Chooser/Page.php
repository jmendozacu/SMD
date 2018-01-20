<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Block_Adminhtml_Widget_Chooser_Page extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct($arguments=array())
    {
        parent::__construct($arguments);

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('skuChooserGrid_'.$this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("$form.chooserGridRowClick.bind($form)");
        $this->setCheckboxCheckCallback("$form.chooserGridCheckboxCheck.bind($form)");
        $this->setRowInitCallback("$form.chooserGridRowInit.bind($form)");
        $this->setDefaultSort('sku');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Retrieve quote store object
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_pages') {
            $selected = $this->_getSelectedProducts();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('page_id', array('in'=>$selected));
            } else {
                $this->getCollection()->addFieldToFilter('page_id', array('nin'=>$selected));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare Catalog Product Collection for attribute SKU in Promo Conditions SKU chooser
     *
     * @return Mage_Adminhtml_Block_Promo_Widget_Chooser_Sku
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('cms/page_collection')
            ->addFieldToSelect(array('title', 'page_id', 'identifier', 'creation_time', 'update_time'));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Define Cooser Grid Columns and filters
     *
     * @return Mage_Adminhtml_Block_Promo_Widget_Chooser_Sku
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_pages', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_pages',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'identifier',
            'use_index' => true,
        ));

        $this->addColumn('page_id', array(
            'header'    => Mage::helper('contentmanager')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'page_id'
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('contentmanager')->__('Identifier'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'identifier'
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('contentmanager')->__('Title'),
            'name'      => 'title',
            'index'     => 'title'
        ));
        
        $this->addColumn('update_time', array(
            'header'    => Mage::helper('contentmanager')->__('Updated At'),
            'name'      => 'update_time',
            'index'     => 'update_time'
        ));
        
        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('contentmanager')->__('Created At'),
            'name'      => 'creation_time',
            'index'     => 'creation_time'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/chooser', array(
            '_current'          => true,
            'current_grid_id'   => $this->getId(),
            'collapse'          => null
        ));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected', array());

        return $products;
    }

}

