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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$conn = $installer->getConnection();

$installer->run(" 

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype')}` (
  `ct_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `default_url` varchar(255) NOT NULL DEFAULT '',
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_robots` varchar(255) NOT NULL DEFAULT '',
  `og_title` varchar(255) NOT NULL DEFAULT '',
  `og_url` text NOT NULL,
  `og_description` text NOT NULL,
  `og_image` text NOT NULL,
  `og_type` text NOT NULL,
  `reviews_enabled` smallint(1) NOT NULL DEFAULT '0',
  `reviews_default_status` int(1) NOT NULL DEFAULT '0',
  `search_enabled` smallint(1) unsigned NOT NULL DEFAULT '0',
  `layout` int(11) NOT NULL DEFAULT '0',
  `root_template` varchar(100) DEFAULT NULL,
  `layout_update_xml` text,
  PRIMARY KEY (`ct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/eav_attribute')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/eav_attribute')}` (
  `attribute_id` smallint(5) unsigned NOT NULL COMMENT 'Attribute ID',
  `is_global` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Is Global',
  `is_searchable` smallint(1) unsigned NOT NULL DEFAULT '0',
  `search_attribute_weight` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT EAV Attribute Table';


-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/content')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/content')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity ID',
  `entity_type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity Type ID',
  `ct_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Type ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Creation Time',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Update Time',
  PRIMARY KEY (`entity_id`),
  KEY `FK_CT_ENTITY_CT_ID_CT_CT_ID` (`ct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Dynamic Content Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_entity_datetime')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_entity_datetime')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Value ID',
  `entity_type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity Type ID',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Option ID',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity ID',
  `value` datetime DEFAULT NULL COMMENT 'Value',
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNQ_CT_DYNA_ENTT_DTIME_ENTT_TYPE_ID_ENTT_ID_ATTR_ID_STORE_ID` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_DATETIME_ENTITY_ID` (`entity_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_DATETIME_ATTR_ID` (`attribute_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_DATETIME_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Dynamic Content Datetime Attribute Backend Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_entity_decimal')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_entity_decimal')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Value ID',
  `entity_type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity Type ID',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Option ID',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity ID',
  `value` decimal(12,4) DEFAULT NULL COMMENT 'Value',
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNQ_CT_DYNA_ENTT_DEC_ENTT_TYPE_ID_ENTT_ID_ATTR_ID_STORE_ID` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_DECIMAL_ENTITY_ID` (`entity_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_DECIMAL_ATTR_ID` (`attribute_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_DECIMAL_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Dynamic Content Decimal Attribute Backend Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_entity_int')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_entity_int')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Value ID',
  `entity_type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity Type ID',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Option ID',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity ID',
  `value` int(11) DEFAULT NULL COMMENT 'Value',
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNQ_CT_DYNA_ENTT_INT_ENTT_TYPE_ID_ENTT_ID_ATTR_ID_STORE_ID` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_INT_ENTITY_ID` (`entity_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_INT_ATTR_ID` (`attribute_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_INT_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Dynamic Content Integer Attribute Backend Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_entity_store')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_entity_store')}` (
  `entity_id` int(10) unsigned NOT NULL COMMENT 'Content ID',
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID',
  PRIMARY KEY (`entity_id`,`store_id`),
  KEY `IDX_BLACKBIRD_CONTENTTYPE_ENTITY_STORE_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Content Entity To Store Linkage Table';

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_entity_text')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_entity_text')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Value ID',
  `entity_type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity Type ID',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Option ID',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity ID',
  `value` text COMMENT 'Value',
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNQ_CT_DYNA_ENTT_TEXT_ENTT_TYPE_ID_ENTT_ID_ATTR_ID_STORE_ID` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_TEXT_ENTITY_ID` (`entity_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_TEXT_ATTR_ID` (`attribute_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_TEXT_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Dynamic Content Text Attribute Backend Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_entity_varchar')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_entity_varchar')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Value ID',
  `entity_type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity Type ID',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Option ID',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity ID',
  `value` varchar(255) DEFAULT NULL COMMENT 'Value',
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNQ_CT_DYNA_ENTT_VCHR_ENTT_TYPE_ID_ENTT_ID_ATTR_ID_STORE_ID` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_VARCHAR_ENTITY_ID` (`entity_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_VARCHAR_ATTR_ID` (`attribute_id`),
  KEY `IDX_CONTENTTYPE_ENTITY_VARCHAR_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Dynamic Content Varchar Attribute Backend Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/fieldset')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/fieldset')}` (
  `fieldset_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Fieldset ID',
  `ct_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Content type ID',
  `title` varchar(255) DEFAULT NULL COMMENT 'Type',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
  PRIMARY KEY (`fieldset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content Type Fieldset Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/flag')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/flag')}` (
  `store_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Fieldset ID',
  `value` varchar(255) DEFAULT NULL COMMENT 'Type',
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content Type Flags Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/indexer_fulltext')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/indexer_fulltext')}` (
  `fulltext_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity ID',
  `entity_id` int(10) unsigned NOT NULL COMMENT 'Product ID',
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID',
  `data_index` longtext COMMENT 'Data index',
  PRIMARY KEY (`fulltext_id`),
  UNIQUE KEY `UNQ_CTSEARCH_FULLTEXT_ENTITY_ID_STORE_ID` (`entity_id`,`store_id`),
  FULLTEXT KEY `FTI_CTSEARCH_FULLTEXT_DATA_INDEX` (`data_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='CT search result table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_layout_block')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_layout_block')}` (
  `layout_block_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Layout Block ID',
  `layout_group_id` int(11) DEFAULT NULL COMMENT 'Layout Group ID',
  `ct_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Type ID',
  `block_id` smallint(6) NOT NULL,
  `html_tag` varchar(50) DEFAULT 'div' COMMENT 'HTML element type',
  `html_id` varchar(255) DEFAULT '' COMMENT 'HTML element id',
  `html_class` varchar(255) DEFAULT '' COMMENT 'HTML element class',
  `label` smallint(3) DEFAULT '0' COMMENT 'Show Label',
  `column` int(11) DEFAULT '0' COMMENT 'Column',
  `sort_order` int(11) DEFAULT '0' COMMENT 'Sort Order',
  `html_label_tag` varchar(50) DEFAULT 'div' COMMENT 'Label HTML element type',
  PRIMARY KEY (`layout_block_id`),
  KEY `FK_CT_LAYOUT_BLOCK_CT_ID_CT_CT_ID` (`ct_id`),
  KEY `FK_CT_LAYOUT_BLOCK_BLOCK_ID_CMS_BLOCK_BLOCK_ID` (`block_id`),
  KEY `FK_CT_LAYOUT_BLOCK_GROUP_ID_GROUP_GROUP_ID` (`layout_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Layout Block Association Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_layout_field')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_layout_field')}` (
  `layout_field_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Layout Field ID',
  `layout_group_id` int(11) DEFAULT NULL COMMENT 'Layout Group ID',
  `ct_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Type ID',
  `option_id` int(10) unsigned DEFAULT NULL,
  `html_tag` varchar(50) DEFAULT 'div' COMMENT 'HTML element type',
  `html_id` varchar(255) DEFAULT '' COMMENT 'HTML element id',
  `html_class` varchar(255) DEFAULT '' COMMENT 'HTML element class',
  `format` varchar(255) DEFAULT '' COMMENT 'Data formating',
  `label` smallint(3) DEFAULT '0' COMMENT 'Show Label',
  `column` int(11) DEFAULT '0' COMMENT 'Column',
  `sort_order` int(11) DEFAULT '0' COMMENT 'Sort Order',
  `html_label_tag` varchar(50) DEFAULT 'div' COMMENT 'Label HTML element type',
  PRIMARY KEY (`layout_field_id`),
  KEY `FK_CT_LAYOUT_FIELD_CT_ID_CT_CT_ID` (`ct_id`),
  KEY `FK_CT_LAYOUT_FIELD_OPTION_ID_CT_CT_ID` (`option_id`),
  KEY `FK_CT_LAYOUT_FIELD_GROUP_ID_GROUP_GROUP_ID` (`layout_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Layout Field Association Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_layout_group')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_layout_group')}` (
  `layout_group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Layout Group ID',
  `parent_layout_group_id` int(11) DEFAULT NULL COMMENT 'Parent Layout Group ID',
  `ct_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Type ID',
  `html_name` varchar(255) DEFAULT '' COMMENT 'HTML element name',
  `html_tag` varchar(50) DEFAULT 'div' COMMENT 'HTML element type',
  `html_id` varchar(255) DEFAULT '' COMMENT 'HTML element id',
  `html_class` varchar(255) DEFAULT '' COMMENT 'HTML element class',
  `label` smallint(3) DEFAULT '0' COMMENT 'Show Label',
  `column` int(11) DEFAULT '0' COMMENT 'Column',
  `sort_order` int(11) DEFAULT '0' COMMENT 'Sort Order',
  `html_label_tag` varchar(50) DEFAULT 'div' COMMENT 'Label HTML element type',
  PRIMARY KEY (`layout_group_id`),
  KEY `FK_CT_LAYOUT_GROUP_CT_ID_CT_CT_ID` (`ct_id`),
  KEY `FK_CT_LAYOUT_GROUP_PARENT_GROUP_ID_GROUP_GROUP_ID` (`parent_layout_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Layout Group Association Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_layout_review')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_layout_review')}` (
  `layout_review_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Layout Review ID',
  `layout_group_id` int(11) DEFAULT NULL COMMENT 'Layout Group ID',
  `ct_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Type ID',
  `html_tag` varchar(50) DEFAULT 'div' COMMENT 'HTML element type',
  `html_id` varchar(255) DEFAULT '' COMMENT 'HTML element id',
  `html_class` varchar(255) DEFAULT '' COMMENT 'HTML element class',
  `title` varchar(255) DEFAULT '' COMMENT 'Title',
  `ask_captcha` smallint(1) DEFAULT '1' COMMENT 'Captcha',
  `ask_lastname` smallint(1) DEFAULT '1' COMMENT 'Lastname',
  `ask_firstname` smallint(1) DEFAULT '1' COMMENT 'Firstname',
  `ask_email` smallint(1) DEFAULT '1' COMMENT 'Email',
  `ask_website` smallint(1) DEFAULT '1' COMMENT 'Website',
  `ask_comment` smallint(1) DEFAULT '1' COMMENT 'Comment',
  `required_lastname` smallint(1) DEFAULT '1' COMMENT 'Required Lastname',
  `required_firstname` smallint(1) DEFAULT '1' COMMENT 'Required Firstname',
  `required_email` smallint(1) DEFAULT '1' COMMENT 'Required Email',
  `required_website` smallint(1) DEFAULT '0' COMMENT 'Required Website',
  `required_comment` smallint(1) DEFAULT '1' COMMENT 'Required Comment',
  `show_lastname` smallint(1) DEFAULT '1' COMMENT 'Lastname',
  `show_firstname` smallint(1) DEFAULT '1' COMMENT 'Firstname',
  `show_email` smallint(1) DEFAULT '0' COMMENT 'Email',
  `show_website` smallint(1) DEFAULT '1' COMMENT 'Website',
  `show_comment` smallint(1) DEFAULT '1' COMMENT 'Comment',
  `show_date` smallint(1) DEFAULT '1' COMMENT 'Date',
  `show_gravatar` smallint(1) DEFAULT '1' COMMENT 'Gravatar',
  `label` smallint(3) DEFAULT '0' COMMENT 'Show Label',
  `column` int(11) DEFAULT '0' COMMENT 'Column',
  `sort_order` int(11) DEFAULT '0' COMMENT 'Sort Order',
  PRIMARY KEY (`layout_review_id`),
  KEY `FK_CT_LAYOUT_REVIEW_CT_ID_CT_CT_ID` (`ct_id`),
  KEY `FK_CT_LAYOUT_REVIEW_GROUP_ID_GROUP_GROUP_ID` (`layout_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CT Layout Block Association Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/menu')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/menu')}` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Menu ID',
  `title` varchar(255) DEFAULT '' COMMENT 'Menu title',
  `identifier` varchar(255) DEFAULT '' COMMENT 'Unique identifier by store view',
  `status` smallint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Menu Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/menu_node')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/menu_node')}` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Node ID',
  `menu_id` int(11) NOT NULL COMMENT 'Menu ID',
  `parent_id` int(11) DEFAULT '0' COMMENT 'Parent ID',
  `type` varchar(32) NOT NULL COMMENT 'The type of this node',
  `entity_id` varchar(255) DEFAULT NULL COMMENT 'The entity referred this node depending on the type',
  `label` varchar(255) DEFAULT NULL COMMENT 'Alternative label for menu entry',
  `target` varchar(20) DEFAULT NULL,
  `classes` varchar(80) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL COMMENT 'Optionally, a custom format to apply to this node',
  `status` smallint(1) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `children_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`node_id`,`menu_id`),
  KEY `FK_CM_MENU_NODE_ID` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodes Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/menu_store')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/menu_store')}` (
  `menu_id` int(11) NOT NULL COMMENT 'Menu ID',
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID',
  PRIMARY KEY (`menu_id`,`store_id`),
  KEY `IDX_CM_MENU_STORE_ID` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CM Menu To Store Linkage Table';

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_option')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_option')}` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Option ID',
  `ct_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Cct ID',
  `type` varchar(50) DEFAULT NULL COMMENT 'Type',
  `is_require` smallint(6) NOT NULL DEFAULT '1' COMMENT 'Is Required',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `attribute_id` smallint(5) unsigned DEFAULT NULL,
  `fieldset_id` int(11) unsigned NOT NULL DEFAULT '0',
  `show_in_grid` smallint(6) NOT NULL DEFAULT '0',
  `note` varchar(255) DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `max_characters` int(11) NOT NULL DEFAULT '0',
  `wysiwyg_editor` smallint(6) NOT NULL DEFAULT '0',
  `crop` smallint(6) NOT NULL DEFAULT '0',
  `crop_w` int(11) NOT NULL DEFAULT '0',
  `crop_h` int(11) NOT NULL DEFAULT '0',
  `keep_aspect_ratio` smallint(6) NOT NULL DEFAULT '0',
  `file_path` varchar(255) DEFAULT NULL,
  `file_extension` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`option_id`),
  KEY `FK_CT_OPTION_ATTRIBUTE_ID` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content Type Option Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_option_title')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_option_title')}` (
  `option_title_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Option Title ID',
  `option_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Option ID',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  `title` varchar(255) DEFAULT NULL COMMENT 'Page Title',
  PRIMARY KEY (`option_title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content Type Option Title Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_option_type_title')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_option_type_title')}` (
  `option_type_title_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Option Type Title ID',
  `option_type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Option Type ID',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store ID',
  `title` varchar(255) DEFAULT NULL COMMENT 'Title',
  PRIMARY KEY (`option_type_title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content Type Option Type Title Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/contenttype_option_type_value')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/contenttype_option_type_value')}` (
  `option_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Option Type ID',
  `option_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Option ID',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
  `value` varchar(255) DEFAULT '0',
  `default` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`option_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content Type Option Type Value Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `{$this->getTable('contentmanager/review')}`
--

CREATE TABLE IF NOT EXISTS `{$this->getTable('contentmanager/review')}` (
  `review_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Entity ID',
  `lastname` varchar(255) NOT NULL DEFAULT '',
  `firstname` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `website` varchar(255) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `FK_CT_REVIEW_CT_ID_CT_CT_ID` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contraintes pour les tables export�es
--

--
-- Contraintes pour la table `{$this->getTable('contentmanager/eav_attribute')}`
--
  
ALTER TABLE `{$this->getTable('contentmanager/eav_attribute')}`
  ADD CONSTRAINT `FK_CT_EAV_ATTRIBUTE_ATTRIBUTE_ID_EAV_ATTRIBUTE_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/content')}`
--
ALTER TABLE `{$this->getTable('contentmanager/content')}`
  ADD CONSTRAINT `FK_CT_ENTITY_CT_ID_CT_CT_ID` FOREIGN KEY (`ct_id`) REFERENCES `{$this->getTable('contentmanager/contenttype')}` (`ct_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_entity_datetime')}`
--
  
ALTER TABLE `{$this->getTable('contentmanager/contenttype_entity_datetime')}`
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_DTIME_ATTR_ID_EAV_ATTR_ATTR_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_DTIME_ENTT_ID_CT_DYNA_ENTT_ENTT_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('contentmanager/content')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_ENTITY_DATETIME_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_entity_decimal')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_entity_decimal')}`
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_DEC_ATTR_ID_EAV_ATTR_ATTR_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_DEC_ENTT_ID_CT_DYNA_ENTT_ENTT_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('contentmanager/content')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_ENTITY_DECIMAL_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_entity_int')}`
--

ALTER TABLE `{$this->getTable('contentmanager/contenttype_entity_int')}`
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_INT_ATTR_ID_EAV_ATTR_ATTR_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_INT_ENTT_ID_CT_DYNA_ENTT_ENTT_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('contentmanager/content')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_ENTITY_INT_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_entity_store')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_entity_store')}`
  ADD CONSTRAINT `FK_CT_ENTITY_STORE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_ENTITY_STORE_ENTITY_ID_CORE_STORE_ENTITY_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('contentmanager/content')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_entity_text')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_entity_text')}`
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_TEXT_ATTR_ID_EAV_ATTR_ATTR_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_TEXT_ENTT_ID_CT_DYNA_ENTT_ENTT_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('contentmanager/content')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_ENTITY_TEXT_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_entity_varchar')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_entity_varchar')}`
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_VCHR_ATTR_ID_EAV_ATTR_ATTR_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_DYNA_ENTT_VCHR_ENTT_ID_CT_DYNA_ENTT_ENTT_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('contentmanager/content')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_ENTITY_VARCHAR_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_layout_block')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_layout_block')}`
  ADD CONSTRAINT `FK_CT_LAYOUT_BLOCK_BLOCK_ID_CMS_BLOCK_BLOCK_ID` FOREIGN KEY (`block_id`) REFERENCES `{$this->getTable('cms/block')}` (`block_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_LAYOUT_BLOCK_CT_ID_CT_CT_ID` FOREIGN KEY (`ct_id`) REFERENCES `{$this->getTable('contentmanager/contenttype')}` (`ct_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_LAYOUT_BLOCK_GROUP_ID_GROUP_GROUP_ID` FOREIGN KEY (`layout_group_id`) REFERENCES `{$this->getTable('contentmanager/contenttype_layout_group')}` (`layout_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_layout_field')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_layout_field')}`
  ADD CONSTRAINT `FK_CT_LAYOUT_FIELD_CT_ID_CT_CT_ID` FOREIGN KEY (`ct_id`) REFERENCES `{$this->getTable('contentmanager/contenttype')}` (`ct_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_LAYOUT_FIELD_GROUP_ID_GROUP_GROUP_ID` FOREIGN KEY (`layout_group_id`) REFERENCES `{$this->getTable('contentmanager/contenttype_layout_group')}` (`layout_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_LAYOUT_FIELD_OPTION_ID_CT_CT_ID` FOREIGN KEY (`option_id`) REFERENCES `{$this->getTable('contentmanager/contenttype_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_layout_group')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_layout_group')}`
  ADD CONSTRAINT `FK_CT_LAYOUT_GROUP_CT_ID_CT_CT_ID` FOREIGN KEY (`ct_id`) REFERENCES `{$this->getTable('contentmanager/contenttype')}` (`ct_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_LAYOUT_GROUP_PARENT_GROUP_ID_GROUP_GROUP_ID` FOREIGN KEY (`parent_layout_group_id`) REFERENCES `{$this->getTable('contentmanager/contenttype_layout_group')}` (`layout_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_layout_review')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_layout_review')}`
  ADD CONSTRAINT `FK_CT_LAYOUT_REVIEW_CT_ID_CT_CT_ID` FOREIGN KEY (`ct_id`) REFERENCES `{$this->getTable('contentmanager/contenttype')}` (`ct_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CT_LAYOUT_REVIEW_GROUP_ID_GROUP_GROUP_ID` FOREIGN KEY (`layout_group_id`) REFERENCES `{$this->getTable('contentmanager/contenttype_layout_group')}` (`layout_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/menu_node')}`
--
ALTER TABLE `{$this->getTable('contentmanager/menu_node')}`
  ADD CONSTRAINT `FK_CM_MENU_NODE_ID` FOREIGN KEY (`menu_id`) REFERENCES `{$this->getTable('contentmanager/menu')}` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/menu_store')}`
--
ALTER TABLE `{$this->getTable('contentmanager/menu_store')}`
  ADD CONSTRAINT `FK_CM_MENU_STORE_MENU_ID` FOREIGN KEY (`menu_id`) REFERENCES `{$this->getTable('contentmanager/menu')}` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CM_MENU_STORE_MENU_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/contenttype_option')}`
--
ALTER TABLE `{$this->getTable('contentmanager/contenttype_option')}`
  ADD CONSTRAINT `FK_CT_OPTION_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `{$this->getTable('contentmanager/review')}`
--
ALTER TABLE `{$this->getTable('contentmanager/review')}`
  ADD CONSTRAINT `FK_CT_REVIEW_CT_ID_CT_CT_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('contentmanager/content')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;


	
");
  
  
/**
 * ATTRIBUTES
 */  

$installer->installEntities();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$attribute  = array(
    'type'          => 'varchar',
    'label'         => 'URL Key',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'        => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'unique'        => true,
    'backend'    => 'contentmanager/content_attribute_backend_urlkey',
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'url_key', $attribute);


/*Title*/
$attribute  = array(
    'type'          => 'varchar',
    'label'         => 'Title',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'title', $attribute);

/*Description*/
$attribute  = array(
    'type'          => 'text',
    'label'         => 'Meta Description',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'description', $attribute);

/*Keywords*/
$attribute  = array(
    'type'          => 'text',
    'label'         => 'Meta Keywords',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'keywords', $attribute);

/*Robots*/
$attribute  = array(
    'type'          => 'varchar',
    'label'         => 'Meta Robots',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'robots', $attribute);

/*OG Title*/
$attribute  = array(
    'type'          => 'varchar',
    'label'         => 'OG Title',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'og_title', $attribute);

/*OG URL*/
$attribute  = array(
    'type'          => 'text',
    'label'         => 'OG Url',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'og_url', $attribute);

/*OG Description*/
$attribute  = array(
    'type'          => 'text',
    'label'         => 'OG Description',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'og_description', $attribute);

/*OG Image*/
$attribute  = array(
    'type'          => 'text',
    'label'         => 'OG Image',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);
$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'og_image', $attribute);

/*OG Type*/
$attribute  = array(
    'type'          => 'text',
    'label'         => 'OG Type',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'comparable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'og_type', $attribute);

/*Title*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Use Default Title',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'use_default_title', $attribute);

/*Status*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Status',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'status', $attribute);

/*Description*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Use Default Meta Description',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'use_default_description', $attribute);

/*Keywords*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Use Default Meta Keywords',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'use_default_keywords', $attribute);

/*Robots*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Use Default Meta Robots',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'use_default_og_title', $attribute);

/*OG URL*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Use Default OG Url',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'use_default_og_url', $attribute);

/*OG Description*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Use Default OG Description',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'use_default_og_description', $attribute);

/*OG Image*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Use Default OG Image',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);
$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'use_default_og_image', $attribute);

/*OG Type*/
$attribute  = array(
    'type'          => 'int',
    'label'         => 'Use Default OG Type',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'searchable'    => false,
    'filterable'    => false,
    'global'            => Blackbird_ContentManager_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'comparable'    => false
);

$setup->addAttribute(Blackbird_ContentManager_Model_Content::ENTITY, 'use_default_og_type', $attribute);
  
  

$installer->endSetup();