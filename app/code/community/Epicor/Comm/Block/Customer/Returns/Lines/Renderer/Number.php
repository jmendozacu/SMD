<?php

class Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Number extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return_Line */
        $html = '';

        if (!Mage::registry('review_display')) {
            $html = '<input type="hidden" name="lines[' . $row->getUniqueId() . '][old_data]" value="' . base64_encode(serialize($row->getData())) . '" />';
            $html .= '<input type="hidden" name="lines[' . $row->getUniqueId() . '][source_data]" value="" />';
            $html .= '<input class="return_line_source_type" type="hidden" name="lines[' . $row->getUniqueId() . '][source_type]" value="' . $row->getSourceType() . '" />';
            $html .= '<input class="return_line_source_value" type="hidden" name="lines[' . $row->getUniqueId() . '][source_value]" value="' . $row->getData($row->getSourceType() . '_number') . '" />';
        }

        $lineCount = Mage::registry('line_count') ? : 1;

        if ($row->getToBeDeleted() == 'Y') {
            $html .= $this->__('To Be Deleted');
        } else {
            $html .= '<span class="return_line_number">' . $lineCount . '</span>';

            $lineCount++;
            if (Mage::registry('line_count')) {
                Mage::unregister('line_count');
            }

            Mage::register('line_count', $lineCount);
        }
        return $html;
    }

}
