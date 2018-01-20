<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Manufacturersproductcode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {

            $rfq = Mage::registry('supplier_connect_rfq_details');
            $selectedManufacturer = $row->getManufacturerCode();
            $selected = $row->getManufacturersProductCode();
            $html = '';

            $html .= '<select class="cross_reference_part_manufacturers_product_code" name="cross_reference_parts[existing][' . $row->getUniqueId() . '][manufacturers_product_code]">';
            if ($rfq->getCrossReferenceManufacturers()) {
                $manufacturers = $rfq->getCrossReferenceManufacturers()->getasarrayCrossReferenceManufacturer();
                foreach ($manufacturers as $x => $manufacturer) {
                    if ($selectedManufacturer == $manufacturer->getManufacturerCode() || count($manufacturers) == 1) {
                        if ($manufacturer->getManufactureParts()) {
                            foreach ($manufacturer->getManufactureParts()->getasarrayProductCode() as $productCode) {
                                $html .= '<option value="' . $productCode . '" ' . (($productCode == $selected) ? 'selected="selected"' : '') . '>' . $productCode . '</option>';
                            }
                        }
                    }
                }
            }
            $html .= '</select>';

            //$html = '<input type="text" class="cross_reference_part_manufacturers_product_code" name="cross_reference_parts[existing][' . $row->getUniqueId() . '][manufacturers_product_code]" value="' . $row->getManufacturersProductCode() . '" />';
        } else {
            $html = $row->getManufacturersProductCode();
        }
        return $html;
    }

}
