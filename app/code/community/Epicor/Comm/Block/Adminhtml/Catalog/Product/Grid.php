<?php

class Epicor_Comm_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /* Overwritten to be able to add custom columns to the product grid. Normally
     * one would overwrite the function _prepareCollection, but it won't work because
     * you have to call parent::_prepareCollection() first to get the collection.
     *
     * But since parent::_prepareCollection() also finishes the collection, the
     * joins and attributes to select added in the overwritten _prepareCollection()
     * are 'forgotten'.
     *
     * By overwriting setCollection (which is called in parent::_prepareCollection()),
     * we are able to add the join and/or attribute select in a proper way.
     *
     */
    public function setCollection($collection)
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */

        $store = $this->_getStore();

        if ($store->getId() && !isset($this->_joinAttributes['uom'])) {
            $collection->joinAttribute(
                'uom',
                'catalog_product/uom',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('uom');
        }
		
		$collection->setFlag('is_catalog_products_grid', true);

        parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        $store = $this->_getStore();
        $this->addColumnAfter('uom',
            array(
                'header'=> Mage::helper('catalog')->__('UOM'),
                'type'  => 'uom',
                'index' => 'uom',
            ),
            'sku'
         );

        return parent::_prepareColumns();
    }
}