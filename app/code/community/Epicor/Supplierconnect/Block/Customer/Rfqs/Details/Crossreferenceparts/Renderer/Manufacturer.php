<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Crossreferenceparts_Renderer_Manufacturer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        if (Mage::registry('rfq_editable')) {

            $oldDetails = array(
                'manufacturer_code' => $row->getManufacturerCode(),
                'manufacturers_product_code' => $row->getManufacturersProductCode(),
                'supplier_product_code' => $row->getSupplierProductCode(),
                'supplier_lead_days' => $row->getSupplierLeadDays(),
                'supplier_reference' => $row->getSupplierReference()
            );

            $html = '<input type="hidden" name="cross_reference_parts[existing][' . $row->getUniqueId() . '][old_data]" value="' . base64_encode(serialize($oldDetails)) . '"/>';

            $rfq = Mage::registry('supplier_connect_rfq_details');
            $selected = $row->getManufacturerCode();

            $html .= '<select class="cross_reference_part_manufacturer" name="cross_reference_parts[existing][' . $row->getUniqueId() . '][manufacturer_code]">';
            if ($rfq->getCrossReferenceManufacturers()) {
                foreach ($rfq->getCrossReferenceManufacturers()->getasarrayCrossReferenceManufacturer() as $x => $manufacturer) {
                    $html .= '<option value="' . $manufacturer->getManufacturerCode() . '" ' . (($manufacturer->getManufacturerCode() == $selected) ? 'selected="selected"' : '') . '>' . $manufacturer->getDescription() . '</option>';
                }
            }
            $html .= '</select>';

            //$html .= '<input type="text" class="cross_reference_part_manufacturer" name="cross_reference_parts[existing][' . $row->getUniqueId() . '][manufacturer]" value="' . $row->getManufacturerCode() . '"/>';
        } else {
            $rfq = Mage::registry('supplier_connect_rfq_details');
            $selected = $row->getManufacturerCode();
            if ($rfq->getCrossReferenceManufacturers()) {
                $html = '';

                foreach ($rfq->getCrossReferenceManufacturers()->getasarrayCrossReferenceManufacturer() as $x => $manufacturer) {
                    if ($manufacturer->getManufacturerCode() == $selected) {
                        $html = $manufacturer->getDescription();
                    }
                }

                if (empty($html)) {
                    $html = $selected;
                }
            } else {
                $html = $selected;
            }
        }
        return $html;
    }

}
