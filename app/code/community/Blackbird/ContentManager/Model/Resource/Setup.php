<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{

    /**
     * Retreive default entities: content
     *
     * @return array
     */
    

    public function getDefaultEntities() {
        
        return array(
            Blackbird_ContentManager_Model_Content::ENTITY => array(
                'entity_model' => 'contentmanager/content',
                'attribute_model' => 'contentmanager/resource_eav_attribute',
                'additional_attribute_table'     => 'contentmanager/eav_attribute',
                'entity_attribute_collection'    => 'contentmanager/content_attribute_collection',
                'table' => 'contentmanager/content', /* Maps to the config.xml > global > models > inchoo_phonebook_resource > entities > user */
                'attributes' => array(
                    'ct_id'               => array(
                        'type'                       => 'static',
                        'label'                      => 'Content Type ID',
                        'required'                   => true,
                        'sort_order'                 => 1,
                        'visible'                    => false
                    ),
                    'created_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'backend'                    => 'eav/entity_attribute_backend_time_created',
                        'sort_order'                 => 2,
                        'visible'                    => false,
                    ),
                    'updated_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'backend'                    => 'eav/entity_attribute_backend_time_updated',
                        'sort_order'                 => 3,
                        'visible'                    => false,
                    ),
                )
            )
        );
    }
}
