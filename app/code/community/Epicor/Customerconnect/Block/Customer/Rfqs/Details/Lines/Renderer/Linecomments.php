<?php

/**
 * RFQ Line comments renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Linecomments extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $key = Mage::registry('rfq_new') ? 'new' : 'existing';
        $index = $this->getColumn()->getIndex();
        $comment = $this->htmlEscape($row->getData($index));

        if (Mage::registry('rfqs_editable')) {
            $html = '<textarea class="lines_additional_text"  name="lines[' . $key . '][' . $row->getUniqueId() . '][additional_text]">' . $comment . '</textarea>';
        } else {
            $html = nl2br($comment) . '<input name="lines[' . $key . '][' . $row->getUniqueId() . '][additional_text]" type="hidden" value="' . $comment . '" />';
        }

        return $html;
    }

}
