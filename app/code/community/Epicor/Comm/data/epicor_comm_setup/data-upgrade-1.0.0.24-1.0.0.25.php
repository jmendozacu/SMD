<?php

/**
 * Version 1.0.0.24 to 1.0.0.25 upgrade
 * 
 * Remove any STG mapping config, as spec has been updated
 */

$config = new Mage_Core_Model_Config();
/* @var $config Mage_Core_Model_Config */

$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_delete');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_level');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_visible');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_group_code');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_parents');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_parent_update');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_parent_level');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_parent_code');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_parent_name');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_parent_description');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_weighting');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/language_code');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/language_name');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_name_update');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/language_description');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_description_update');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/image_filename');
$config->deleteConfig('epicor_comm_field_mapping/stg_mapping/group_image_filename_update');

Mage::app()->cleanCache(array('CONFIG'));