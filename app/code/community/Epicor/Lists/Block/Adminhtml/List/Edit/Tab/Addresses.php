<?php

/**
 * List Addresses Serialized Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Addresses extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('addressesGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        if ($this->isSectionEditable()) {
            $this->setRowInitCallback("initListAddress('addresses_form','addressesGrid');");
        }
    }

    /**
     * Is this tab shown?
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab Label
     *
     * @return boolean
     */
    public function getTabLabel()
    {
        return 'Addresses';
    }

    /**
     * Tab Title
     *
     * @return boolean
     */
    public function getTabTitle()
    {
        return 'Addresses';
    }

    /**
     * Is this tab hidden?
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Gets the List for this tab
     *
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        if (!$this->list) {
            if (Mage::registry('list')) {
                $this->list = Mage::registry('list');
            } else {
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->list;
    }

    /**
     * Build data for List Addresses
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Addresses
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_lists/list_address')->getCollection();
        /* @var $collection Epicor_Lists_Model_Resource_List_Address_Collection */
        $collection->addFieldToFilter('list_id', $this->getList()->getId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List Addresses
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Addresses
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $this->addColumn(
            'address_code', array(
            'header' => $helper->__('Address Code'),
            'index' => 'address_code',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'purchase_order_number', array(
            'header' => $helper->__('Purchase Order Number'),
            'index' => 'purchase_order_number',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'address_name', array(
            'header' => $helper->__('Name'),
            'index' => 'name',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'flatt_address', array(
            'header' => $helper->__('Address'),
            'index' => 'address1',
            'type' => 'text',
            'renderer' => 'epicor_lists/adminhtml_widget_grid_column_renderer_list_address',
            'filter_condition_callback' => array($this, '_addressFilter'),
            )
        );

        $this->addColumn(
            'address_email_address', array(
            'header' => $helper->__('Email'),
            'index' => 'email_address',
            'type' => 'text'
            )
        );

        if ($this->getList()->getType() == 'Co') {
            $this->addColumn(
                'activation_date', array(
                'header' => $helper->__('Activation Date'),
                'index' => 'activation_date',
                'type' => 'datetime'
                )
            );

            $this->addColumn(
                'expiry_date', array(
                'header' => $helper->__('Expiry Date'),
                'index' => 'expiry_date',
                'type' => 'datetime'
                )
            );
        }

        if ($this->isSectionEditable()) {
            $this->addColumn('actions', array(
                'header' => $helper->__('Actions'),
                'width' => '100',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $helper->__('Edit'),
                        'onclick' => 'javascript: listAddress.rowEdit(this)',
                    ),
                    array(
                        'caption' => $helper->__('Delete'),
                        'onclick' => 'javascript: if(window.confirm(\''
                        . addslashes($this->escapeHtml($helper->__('Are you sure you want to do this?')))
                        . '\')){listAddress.rowDelete(this);} return false;',
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
                'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
                'links' => 'true',
            ));

            $this->addColumn('rowdata', array(
                'header' => Mage::helper('flexitheme')->__(''),
                'align' => 'left',
                'width' => '1',
                'name' => 'rowdata',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_rowdata',
                'column_css_class' => 'no-display last',
                'header_css_class' => 'no-display last',
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _addressFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $clone = clone $collection;

        $filterIds = array();
        foreach ($clone->getItems() as $item) {
            /* @var $item Epicor_Lists_Model_List */
            if (stripos($item->getFlattenedAddress(), $value) !== false) {
                $filterIds[] = $item->getId();
            }
        }

        $collection->addFieldToFilter('id', array('in' => $filterIds));
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getList()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorlists_list/addressesgrid', $params);
    }

    /**
     * Row Click URL
     *
     * @param Epicor_Lists_Model_List_Address $row
     * 
     * @return null
     */
    public function getRowUrl($row)
    {
        return null;
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    protected function _prepareLayout()
    {
        if ($this->isSectionEditable()) {
            $this->setChild(
                'add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('adminhtml')->__('Add'),
                        'onclick' => "listAddress.add();",
                        'class' => 'task'
                    ))
            );
        }
        return parent::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $html = $this->isSectionEditable() ? $this->getAddButtonHtml() : '';
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    public function isSectionEditable()
    {
        return $this->getList()->getTypeInstance()->isSectionEditable('addresses');
    }

}
