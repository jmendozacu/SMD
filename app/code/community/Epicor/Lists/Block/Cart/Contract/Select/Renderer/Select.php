<?php

/**
 * Column Renderer for Line Contract Select Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Cart_Contract_Select_Renderer_Select extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render column
     *
     * @param   Epicor_Lists_Model_List $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $item = Mage::registry('ecc_line_contract_item');
        /* @var $item Epicor_Comm_Model_Quote_Item */

        if ($row->getErpCode() == $item->getEccContractCode()) {
            $html = $this->__('Currently Selected');
        } else {
            $urlReturn = Mage::app()->getRequest()->getParam('return_url');
            $params = array(
                'itemid' => $item->getId(),
                'contract' => $row->getId(),
                'return_url' => base64_encode($this->helper('epicor_comm')->urlEncode($urlReturn))
            );
            $url = Mage::getUrl('epicor_lists/cart/applycontractselect', $params);
            $html = '<a href="' . $url . '">' . $this->__('Select') . '</a>';
        }

        return $html;
    }

}
