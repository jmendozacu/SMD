<?php

/**
 * Generic totals rows display class
 * 
 * Used with generic grids to add totals rows
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Generic_Totals extends Mage_Core_Block_Template {

    private $_rows = array();

    public function _construct() {
        parent::_construct();
        $this->setTemplate('epicor_common/totals.phtml');
        $this->setColumns(1);
    }

    /**
     * Adds a row to the totals display
     * 
     * @param string $label
     * @param string $value
     * @param string $labelClass
     * @param string $valueClass
     */
    protected function addRow($label, $value, $labelClass = '', $valueClass = '') {
        $this->_rows[] = array(
            'label' => $label,
            'value' => $value,
            'label_class' => $labelClass,
            'value_class' => $valueClass
        );
    }

    /**
     * gets the row array
     * 
     * @return array
     */
    public function getRows() {
        return $this->_rows;
    }

    /**
     * Gets the helper
     * 
     * @return Epicor_Common_Helper_Data
     */
    public function getHelper($type = null) {
        return Mage::helper('epicor_common');
    }

}