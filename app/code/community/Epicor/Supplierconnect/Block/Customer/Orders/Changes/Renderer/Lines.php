<?php

/**
 * Lines list display (expanded by expand column)
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Changes_Renderer_Lines extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $helper = Mage::helper('supplierconnect');
        /* @var $helper Epicor_Supplierconnect_Helper_Data */

        $html = '';

        $lines = ($row->getLines()) ? $row->getLines()->getasarrayLine() : array();
        
        $disabled = (!Mage::registry('orders_editable')) ? 'disabled="disabled"' : '';
        
        if (count($lines) > 0) {
            $html = '</td></tr><tr id="row-changes-' . $row->getId() . '" style="display: none;"><td colspan="9" class="lines-row">
            <table class="expand-table">
                <thead>
                    <tr class="headings">
                        <th>' . $this->__('Confirm') . '</th>
                        <th>' . $this->__('Reject') . '</th>
                        <th>' . $this->__('PO Line') . '</th>
                        <th>' . $this->__('Release Number') . '</th>
                        <th>' . $this->__('Part Number') . '</th>
                        <th>' . $this->__('Supplier Part number') . '</th>
                        <th>' . $this->__('Order Qty') . '</th>
                        <th>' . $this->__('Orig Release Qty') . '</th>
                        <th>' . $this->__('New Release Qty') . '</th>
                        <th>' . $this->__('Orig Due Date') . '</th>
                        <th>' . $this->__('New Due Date') . '</th>
                    </tr>
                </thead>
                <tbody>
            ';

            foreach ($lines as $line) {

                $origDueDate = $line->getOrigValues()->getDueDate();
                $origDueDate = !empty($origDueDate) ? $helper->getLocalDate($origDueDate, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM) : $this->__('N/A');
                $newDueDate = $line->getNewValues()->getDueDate();
                $newDueDate = !empty($newDueDate) ? $helper->getLocalDate($newDueDate, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM) : $this->__('N/A');

                $html .= '
                  <tr>
                    <td><input type="checkbox" name="actions[' . $row->getId() . '][' . $line->getPurchaseOrderLineNumber() . ']['.$line->getReleaseNumber().']" value="C" id="po_confirm_' . $row->getId() . '_' . $line->getPurchaseOrderLineNumber() . '_'.$line->getReleaseNumber().'" class="po_confirm" '.$disabled.'/></td>
                    <td><input type="checkbox" name="actions[' . $row->getId() . '][' . $line->getPurchaseOrderLineNumber() . ']['.$line->getReleaseNumber().']" value="R" id="po_reject_' . $row->getId() . '_' . $line->getPurchaseOrderLineNumber() . '_'.$line->getReleaseNumber().'" class="po_reject" '.$disabled.'/></td>
                    <td>' . $line->getPurchaseOrderLineNumber() . '</td>
                    <td>' . $line->getReleaseNumber() . '</td>
                    <td>' . $line->getProductCode() . '</td>
                    <td>' . $line->getSupplierProductCode() . '</td>
                    <td>' . $line->getQuantity() . '</td>
                    <td>' . $line->getOrigValues()->getReleaseQuantity() . '</td>
                    <td>' . $line->getNewValues()->getReleaseQuantity() . '</td>
                    <td>' . $origDueDate . '</td>
                    <td>' . $newDueDate . '</td>
                  </tr>
                    ';
            }
            $html .= '</tbody></table>';
        }

        return $html;
    }

}

