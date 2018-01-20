<?php

/**
 * Line releases display
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Linereleases extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $html = '';
        $orderDisplay = Mage::registry('supplier_connect_order_display');

        $releases = ($row->getReleases()) ? $row->getReleases()->getasarrayRelease() : array();

        if (count($releases) > 0) {

            $html = '</td></tr><tr id="row-releases-' . $row->getId() . '" style="display: none;"><td colspan="11">
            <table class="expand-table">
                <thead>
                    <tr class="headings">
                        <th>' . $this->__('Releases') . '</th>
                        <th>' . $this->__('Release') . '</th>
                        <th>' . $this->__('Due Date') . '</th>
                        <th>' . $this->__('Ordered Qty') . '</th>
                        <th>' . $this->__('Received Qty') . '</th>
                        <th>' . $this->__('Request Changes') . '</th>
                        <th>' . $this->__('New Date') . '</th>
                        <th>' . $this->__('New Qty') . '</th>
                        <th>' . $this->__('Comment') . '</th>
                    </tr>
                </thead>
                <tbody>
            ';
            $format = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            foreach ($releases as $release) {
                $helper = Mage::helper('supplierconnect');

                $number = $release['_attributes']->getNumber();
                $changed = $release['changed'];
                $quantity = $release['changed_quantity'] > 0 ? $release['changed_quantity'] : $release['quantity'];
                $comment = $release['comment'];
                $date = $release['changed_due_date'] ? Mage::helper('supplierconnect')->getLocalDate($release['changed_due_date'], Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : '';

                if ($orderDisplay == 'edit' && $changed == 'true') {
                    $fieldsDisabled = 'disabled="disabled"';

                    $changedField = '<input name="purchase_order[lines][' . $row->getId() . '][releases][' . $number . '][changed]" type="checkbox" class="purchase_order_changed"/>';
                    $dueDateField = '<input id="line_' . $row->getId() . '_release_' . $number . '_date" name="purchase_order[lines][' . $row->getId() . '][releases][' . $number . '][changed_due_date]" type="text" value="' . $date . '" ' . $fieldsDisabled . '/>
                        <script type="text/javascript">// <![CDATA[
                        Calendar.setup({
                            inputField : \'line_' . $row->getId() . '_release_' . $number . '_date\',                           
                            ifFormat : \'' . $format . '\',
                            button : \'date_from_trig\',
                            align : \'Bl\',
                            singleClick : true
                        });
                        // ]]></script>';
                    $quantityField = '<input name="purchase_order[lines][' . $row->getId() . '][releases][' . $number . '][changed_quantity]" type="text" value="' . $quantity . '" ' . $fieldsDisabled . '/>';
                    $commentField = '<textarea name="purchase_order[lines][' . $row->getId() . '][releases][' . $number . '][comment]" ' . $fieldsDisabled . '>' . $comment . '</textarea>';
                } else {                
                    $changedField = '<input name="purchase_order[lines][' . $row->getId() . '][releases][' . $number . '][changed]" type="hidden" class="purchase_order_changed" value="' . $changed . '"/>';
                    $dueDateField = $date . '<input id="line_' . $row->getId() . '_release_' . $number . '_date" name="purchase_order[lines][' . $row->getId() . '][releases][' . $number . '][changed_due_date]" type="hidden" value="' . $date . '"/>';
                    $quantityField = $quantity . '<input name="purchase_order[lines][' . $row->getId() . '][releases][' . $number . '][changed_quantity]" type="hidden" value="' . $quantity . '" />';
                    $commentField = $comment . '<input type="hidden" name="purchase_order[lines][' . $row->getId() . '][releases][' . $number . '][comment]" value="' . $comment . '"/>';
                }

                $html .= '
                  <tr>
                    <td></td>
                    <td>' . $number . '</td>
                    <td>' . ($release['due_date'] ? Mage::helper('supplierconnect')->getLocalDate($release['due_date'], Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : $this->__('N/A')) . '</td>
                    <td>' . $release['quantity'] . '</td>
                    <td>' . $release['received_quantity'] . '</td>
                    <td>' . $changedField . '</td>
                    <td>' . $dueDateField . '</td>
                    <td>' . $quantityField . '</td>
                    <td>' . $commentField . '</td>
                  </tr>
                    ';
            }
            $html .= '</tbody></table>';
        }

        return $html;
    }

}
