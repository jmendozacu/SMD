<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 15/11/2016
 * Time: 07:48
 */

class Webtise_Gallery_Block_Adminhtml_Custom_Block_References extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Webtise_Gallery_Block_Adminhtml_Custom_Block_References constructor
     */
    public function __construct()
    {
        $this->addColumn('label', array(
            'label' => Mage::helper('gallery')->__('Block Label'),
            'size' => 25
        ));
        $this->addColumn('xml_name', array(
            'label' => Mage::helper('gallery')->__('Name in xml'),
            'size' => 25
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('gallery')->__('Add Block');

        parent::__construct();
    }
}