<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'catalog_product' => array(
                'entity_model'      => 'catalog/product',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes'        => array(
                    'downloads_title' => array(
                        'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Downloads Title',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'visible'           => false,
                        'required'          => false,
                        'user_defined'      => false,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'visible_in_advanced_search' => false,
                        'used_in_product_listing' => false,
                        'used_for_sort_by'  => false,
                        'unique'            => false,
                    )
                )
            )
        );
    }
}
