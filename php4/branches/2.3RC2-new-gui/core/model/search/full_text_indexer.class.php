<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/core/model/search/search_text_normalizer_factory.class.php');

class full_text_indexer
{
  var $db = null;
  var $string_normalizer = null;

  function full_text_indexer()
  {
    $this->db = db_factory :: instance();
  }

  function is_enabled()
  {
    if(!defined('FULL_TEXT_INDEXER_ENABLED'))
      return true;

    return constant('FULL_TEXT_INDEXER_ENABLED');
  }

  function & instance()
  {
    $obj =&	instantiate_object('full_text_indexer');
    return $obj;
  }

  function add(&$site_object)
  {
    //quick and dirty solution of the fundamental problem
    //which should be solved in 3.0 ... :(
    if(!$this->is_enabled())
      return;

    $indexer =& full_text_indexer :: instance();

    $indexer->remove($site_object);

    $attributes =& $site_object->export_attributes();

    reset($attributes);
    $keys = array_keys($attributes);

    foreach($keys as $attribute_name)
    {
      $definition = $site_object->get_attribute_definition($attribute_name);

      if (!isset($definition['search']) || !$definition['search'])
        continue;

      $weight = isset($definition['search_weight']) ? $definition['search_weight'] : 1;

      $normalizer_name = isset($definition['search_text_normalizer']) ? $definition['search_text_normalizer'] : 'search_text_normalizer';

      if($text =& $indexer->normalize_string($attributes[$attribute_name], $normalizer_name))
      {
        $indexer->db->sql_insert('sys_full_text_index',
          array(
            'body' => $text,
            'attribute' => $attribute_name,
            'weight' => $weight,
            'object_id' => $site_object->get_id(),
            'class_id' => $site_object->get_class_id()
          )
        );
      }
    }
  }

  function remove(&$site_object)
  {
    $indexer =& full_text_indexer :: instance();

    $indexer->db->sql_delete('sys_full_text_index', array('object_id' => $site_object->get_id()));
  }

  function & normalize_string(&$content, $normalizer_name='search_text_normalizer')
  {
    $text_normalizer =& search_text_normalizer_factory :: instance($normalizer_name);

    return $text_normalizer->process($content);
  }
}

?>