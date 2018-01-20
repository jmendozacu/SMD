<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Customerconnect_Block_Customer_Account_Shippingaddress_List_Grid extends Epicor_Common_Block_Generic_List_Grid {

    private $_allowEdit;
    private $_allowDelete;
    
    public function __construct() {
        parent::__construct();

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        
        $this->_allowEdit = $helper->customerHasAccess('Epicor_Customerconnect','Account','saveShippingAddress','','Access');
        $this->_allowDelete = $helper->customerHasAccess('Epicor_Customerconnect','Account','deleteShippingAddress','','Access');
        if(!Mage::helper('customerconnect')->checkMsgAvailable('CUAU')){
            $this->_allowEdit = false;
            $this->_allowDelete = false;
        }
        
        if($this->_allowEdit) {
            $this->setRowClickCallback('editShippingAddress');
        }
        
        $this->setId('customer_account_shippingaddress_list');
        $this->setIdColumn('id');
        $this->setDefaultSort('name');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setMessageBase('customerconnect');
        $this->setCustomColumns($this->_getColumns());
        $this->setCacheDisabled(true);
        $this->setUseAjax(true);
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        //       $this->setRowUrlValue('*/*/editShippingAddress');

        $details = Mage::registry('customer_connect_account_details');
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        if($details) {
            /* @var $order Epicor_Common_Model_Xmlvarien */
            $shippingAddresses = $details->getVarienDataArrayFromPath('delivery_addresss/delivery_address') ? : $details->getVarienDataArrayFromPath('delivery_addresses/delivery_address');
            $this->setCustomData($shippingAddresses);

            // this foreach is to replace the name field with shipping_name 
            // this avoids the problem of searching on a grid when two grids are on one page   
            // but having the filter applied to both grids (shipping and contacts share the same page and have a duplicated field of name)
            $customDataArray  = $this->getCustomData();
            foreach ($customDataArray as $key=>$customData){
                $customData->setShippingName($customData->getName());
            }
        } else {
            $this->setCustomColumns(array());
            $this->setCustomData(array());            
            $this->setFilterVisibility(false);
            $this->setPagerVisibility(false);
        }
        
    }

    protected function _getColumns() {
        $columns = array(
            'shipping_name' => array(
                'header' => Mage::helper('epicor_comm')->__('Name'),
                'align' => 'left',
                'index' => 'shipping_name',
                'width' => '100px',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Account_Shippingaddress_Renderer_Shippingaddress(),
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'address1' => array(
                'header' => Mage::helper('epicor_comm')->__('Address1'),
                'align' => 'left',
                'index' => 'address1',
                'width' => '150px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'address2' => array(
                'header' => Mage::helper('epicor_comm')->__('Address2'),
                'align' => 'left',
                'index' => 'address2',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'address3' => array(
                'header' => Mage::helper('epicor_comm')->__('Address3'),
                'align' => 'left',
                'index' => 'address3',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'city' => array(
                'header' => Mage::helper('epicor_comm')->__('City'),
                'align' => 'left',
                'index' => 'city',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'postcode' => array(
                'header' => Mage::helper('epicor_comm')->__('Postcode'),
                'align' => 'left',
                'index' => 'postcode',
                'type' => 'postcode',
                'condition' => 'LIKE'
            ),
            'country' => array(
                'header' => Mage::helper('epicor_comm')->__('Country'),
                'align' => 'left',
                'index' => 'country',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'county' => array(
                'header' => Mage::helper('epicor_comm')->__('State/Province'),
                'align' => 'left',
                'index' => 'county',
                'condition' => 'LIKE',
                'type' => 'state',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Account_Shippingaddress_Renderer_State(),
            ),
            'telephone_number' => array(
                'align' => 'left',
                'index' => 'telephone_number',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
            ),
            'fax_number' => array(
                'align' => 'left',
                'index' => 'fax_number',
                'display' => 'hidden',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
            )
        );
        
        if($this->_allowEdit || $this->_allowDelete) {
            $columns['action'] = array(
                'header' => Mage::helper('customerconnect')->__('Action'),
                'id' => 'action-select',
                'type' => 'action',
                'actions' => array(),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            );
            
            if($this->_allowEdit) {
                $columns['action']['actions'][] = array(
                    'caption' => Mage::helper('customerconnect')->__('Edit'),
                    'url' => "javascript:;",
                );
            }
            
            if($this->_allowDelete) {
                $columns['action']['actions'][] = array(
                    'caption' => Mage::helper('customerconnect')->__('Delete'),
                    'url' => "javascript:;",
                    'confirm' => Mage::helper('customerconnect')->__('Are you sure you want to delete this address?  This action cannot be undone.')
                );
            }
        }

        return $columns;
    }

    public function getRowUrl($row) {
//        $row->unsShippingName();
        return false;
    }
    
    public function getGridUrl()
    {        
            return $this->getUrl('*/grid/shippingsearch');	// this determines which url and tab are displayed after search is complete  		
    }

}
