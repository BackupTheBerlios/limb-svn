<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
if(isset($argv[1]))
	$project_dir = $argv[1];
else
	die('project dir required');

require_once($project_dir . '/setup.php');
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

$db =& db_factory :: instance();

$sql = "SELECT id
        FROM sys_class
        WHERE class_name in
        ( 'ad_block_folder',
          'admin_page',
          'announce_folder',
          'articles_folder',
          'cache_manager',
          'cart_object',
          'catalog_folder',
          'chat',
          'control_panel',
          'controller_folder',
          'documents_folder',
          'faq_folder',
          'faq_folder_container',
          'file_select',
          'files_folder',
          'guestbook',
          'image_select',
          'images_folder',
          'informer_folder',
          'links_manager_page',
          'login_object',
          'news_folder',
          'node_select',
          'not_found_page',
          'objects_access',
          'paragraphs_list_page',
          'period_news_folder',
          'photogallery_folder',
          'pictured_news_folder',
          'pricelist_folder',
          'search_object',
          'simple_orders_folder',
          'site_map',
          'site_param_object',
          'site_structure',
          'stats_event',
          'stats_report',
          'subscribe',
          'template_source',
          'useful_links_folder',
          'user_activate_password',
          'user_change_password',
          'user_generate_password',
          'user_groups_folder',
          'users_folder',
          'version'
         );";
$db->sql_exec($sql);
$odd_class_ids = $db->get_array();
$result = array();


foreach($odd_class_ids as $class)
  $result[$class['id']] = $class['id'];

$odd_class_ids = "'". implode("','", $result) . "'";

$sql = "SELECT id
        FROM sys_class
        WHERE class_name = 'site_object';";
$db->sql_exec($sql);
$site_object = reset($db->get_array());

$sql = "UPDATE sys_site_object
        SET class_id = '{$site_object['id']}'
        WHERE class_id in ({$odd_class_ids})";
$db->sql_exec($sql);

$sql = "DELETE FROM sys_class
        WHERE id in ({$odd_class_ids})";

$db->sql_exec($sql);


echo 'done';

?>