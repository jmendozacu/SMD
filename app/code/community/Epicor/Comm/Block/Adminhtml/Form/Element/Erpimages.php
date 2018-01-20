<?php

/**
 * ERP Images attribute renderer - renders serialized data
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Form_Element_Erpimages extends Epicor_Common_Lib_Varien_Data_Form_Element_Serialized {

    protected $_allowAdd = false;
    protected $_trackRowDelete = true;
    protected $_columns = array(
        'filename' => array(
            'type' => 'static',
            'label' => 'Filename'
        ),
        'description' => array(
            'type' => 'static',
            'label' => 'Description'
        ),
        'types' => array(
            'type' => 'static',
            'label' => 'Types',
            'renderer' => 'Epicor_Comm_Block_Adminhtml_Renderer_Erpimages_Types'
        ),
        'position' => array(
            'type' => 'static',
            'label' => 'Position',
        ),
        'stores' => array(
            'type' => 'static',
            'label' => 'Stores',
            'renderer' => 'Epicor_Comm_Block_Adminhtml_Renderer_Erpimages_Stores'
        ),
        'status' => array(
            'type' => 'static',
            'label' => 'Status',
            'default' => '0',
            'renderer' => 'Epicor_Comm_Block_Adminhtml_Renderer_Erpimages_Status'
        ),
        'attachment_number' => array(
            'type' => 'static',
            'label' => 'Attachment Id',
        ),
        'erp_file_id' => array(
            'type' => 'static',
            'label' => 'Erp File Id',
        ),
        'url' => array(
            'type' => 'static',
            'label' => 'Erp Attachment Url',
        )
    );

}
