<?php

/**
 * RFQ attachments editable file field renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Attachments_Renderer_Filename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $rfq = Mage::registry('current_rfq_row');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */

        $helper = Mage::helper('epicor_common/file');
        /* @var $helper Epicor_Common_Helper_File */
        
        $index = $this->getColumn()->getIndex();
        $value = $row->getData($index);

        $url = $helper->getFileUrl($row->getWebFileId(), $row->getErpFileId(), $row->getFilename(), $row->getUrl());

        $html = $value . ' <a href="' . $url . '" target="_blank" class="attachment_view">' . $this->__('View') . '</a>';

        if (Mage::registry('rfqs_editable') || Mage::registry('rfqs_editable_partial')) {
            $html .= ' | ' . $this->__('Update File') . ': <input type="file" name="lineattachments[' . $key . '][' . $rfq->getUniqueId() . '][' . $row->getUniqueId() . '][' . $index . ']" class="line_attachments_' . $index . '"/>';
        }

        return $html;
    }

}
