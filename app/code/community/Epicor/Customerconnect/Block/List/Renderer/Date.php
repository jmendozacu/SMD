<?php

/**
 * Currency display, converts a row value to currency display
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_List_Renderer_Date
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $index = $this->getColumn()->getIndex();

        $date = $row->getData($index);
        $data = '';

        if (!empty($date)) {
            try {
                $data = $helper->getLocalDate($row->getData($index), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
            } catch (Exception $ex) {
                $data = $row->getData($index);
            }
        }

        return $data;
    }

}
