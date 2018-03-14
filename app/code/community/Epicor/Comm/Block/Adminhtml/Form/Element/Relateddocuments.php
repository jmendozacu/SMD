<?php

/**
 * Related documents attribute renderer - renders serialized data
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Form_Element_Relateddocuments extends Epicor_Common_Lib_Varien_Data_Form_Element_Serialized {

    protected $_columns = array(
        'filename' => array(
            'type' => 'text',
            'label' => 'Filename'
        ),
        'description' => array(
            'type' => 'text',
            'label' => 'Description'
        ),
        'url' => array(
            'type' => 'text',
            'label' => 'Url'
        ),
        'attachment_number' => array(
            'type' => 'text',
            'label' => 'Attachment Number'
        ),
        'erp_file_id' => array(
            'type' => 'text',
            'label' => 'ERP File Id'
        ),
        'web_file_id' => array(
            'type' => 'text',
            'label' => 'Web File Id'
        ),
        'attachment_status' => array(
            'type' => 'text',
            'label' => 'Attachment Status'
        ),
        'sync_required' => array(
            'type' => 'static',
            'label' => 'Sync Required',
            'default' => '0',
            'renderer' => 'Epicor_Comm_Block_Adminhtml_Renderer_Relateddocuments_Status'
        ),
        'is_erp_document' => array(
            'type' => 'checkbox',
            'label' => 'From ERP?',
            'disabled' => true,
            'default' => 0
        )
    );

}