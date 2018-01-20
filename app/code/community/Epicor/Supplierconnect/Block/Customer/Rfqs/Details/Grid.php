<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Supplierconnect_Block_Customer_Rfqs_QuantityPriceBreaks_Qpb_Grid extends Epicor_Common_Block_Generic_List_Grid 
{
    public function __construct() {
        parent::__construct();

        $this->setId('supplierconnect_quantity_price_breaks');
        $this->setDefaultSort('manufacturer');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('surd');
        $this->setIdColumn('price_break_code');
        $this->setDataSubset('price_breaks/price_break');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $rfq = Mage::registry('supplier_connect_rfq_details');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */
        $this->setCustomData((array)$rfq->getVarienDataArrayFromPath('price_breaks/price_break'));
   
    }

    protected function _getColumns() {
       
        $columns = array();
        
        if (Mage::registry('rfq_editable')) {
            $columns['delete_option'] = array(
                'header' => Mage::helper('epicor_comm')->__("Delete"),
                'align' => 'left',
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_QuantityPriceBreaks_Qpb_Renderer_Delete(),
                'type' => 'checkbox'
            );
        }

        $columns['price_break_code'] = array(
            'header' => Mage::helper('epicor_comm')->__("Price Break Code"),
            'align' => 'left',
            'index' => 'price_break_code',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_QuantityPriceBreaks_Qpb_Renderer_PriceBreakCode(),
            'type' => 'input'
        );
        
        $columns['quantity'] = array(
            'header' => Mage::helper('epicor_comm')->__("Minimum Qty"),
            'align' => 'left',
            'index' => 'quantity',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_QuantityPriceBreaks_Qpb_Renderer_MinQuantity(),
            'type' => 'input'
        );
        
        $columns['modified'] = array(
            'header' => Mage::helper('epicor_comm')->__("Modified"),
            'align' => 'left',
            'index' => 'modified',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_QuantityPriceBreaks_Qpb_Renderer_Modified(),
            'type' => 'input'
        );
        
        $columns['effective_price'] = array(
            'header' => Mage::helper('epicor_comm')->__("Effective Price"),
            'align' => 'left',
            'index' => 'effective_price',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Rfqs_QuantityPriceBreaks_Qpb_Renderer_EffectivePrice(),
            'type' => 'input'
        );


        return $columns;
    }
}