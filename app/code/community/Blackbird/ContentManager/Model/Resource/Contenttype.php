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

class Blackbird_ContentManager_Model_Resource_ContentType extends Mage_Core_Model_Mysql4_Abstract
{
    public static $identifierById = array();
    
    public function _construct(){
        $this->_init('contentmanager/contenttype', 'ct_id');
    }
    
    public function getIdentifierById($ctid)
    {
        if(!$ctid)
        {
            return 'everything';
        }
        
        if(!isset(self::$identifierById[$ctid]))
        {
            $adapter = $this->_getReadAdapter();

            $select  = $adapter->select()
                ->from($this->getTable('contentmanager/contenttype'), 'identifier')
                ->where('ct_id = :ct_id');

            $binds = array(
                ':ct_id' => (int) $ctid
            );
            
            $result = $adapter->fetchCol($select, $binds);
            if($result)
            {
                self::$identifierById[$ctid] = $result[0];
            }
            else {
                return 'everything';
            }
        }

        return self::$identifierById[$ctid];
    }
}

