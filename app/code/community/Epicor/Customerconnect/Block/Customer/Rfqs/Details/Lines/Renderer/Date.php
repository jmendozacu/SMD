<?php

/**
 * RFQ Line row date renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $index = $this->getColumn()->getIndex();
        $date = $row->getData($index);
        $data = '';

        if (!empty($date)) {
            try {
                $data = $helper->getLocalDate($row->getData($index), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            } catch (Exception $ex) {
                $data = $row->getData($index);
            }
        }

        if (Mage::registry('rfqs_editable')) {
            $row->getProduct()->setData($index, $data);
            $format = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $html = '<input id="line_' . $row->getUniqueId() . '_request_date" name="lines[' . $key . '][' . $row->getUniqueId() . '][request_date]" type="text" value="' . $data . '"  class="lines_request_date"/>
                     <script type="text/javascript">// <![CDATA[
                        Calendar.setup({
                            inputField : \'line_' . $row->getUniqueId() . '_request_date\',                           
                            ifFormat : \'' . $format . '\',
                            button : \'date_from_trig\',
                            align : \'Bl\',
                            singleClick : true
                        });
                        // ]]></script>';
        } else {
            $html = $data;
            $html .= '<input id="line_' . $row->getUniqueId() . '_request_date" name="lines[' . $key . '][' . $row->getUniqueId() . '][request_date]" type="hidden" value="' . $data . '"  class="lines_request_date"/>';
        }

        return $html;
    }

}
