<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(dirname(__FILE__) . '/../normalizers/search_text_normalizer_factory.class.php');

class full_text_indexer
{
  static protected $instance;

  protected $db = null;
  protected $string_normalizer;

  function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
  }

  static public function instance()
  {
    if (!self :: $instance)
      self :: $instance = new full_text_indexer();

    return self :: $instance;
  }

  static public function add($site_object)
  {
    full_text_indexer :: instance()->_do_add($site_object);
  }

  protected function _do_add($site_object)
  {
    $this->remove($site_object);

    $attributes = $site_object->export();

    reset($attributes);
    $keys = array_keys($attributes);

    foreach($keys as $attribute_name)
    {
      $definition = $site_object->get_behaviour()->get_definition($attribute_name);

      if (!isset($definition['search']) || !$definition['search'])
        continue;

      $weight = isset($definition['search_weight']) ? $definition['search_weight'] : 1;

      $normalizer_name = isset($definition['search_text_normalizer'])
                          ? $definition['search_text_normalizer']
                          : 'search_text_normalizer';

      if($text = $this->_normalize_string($attributes[$attribute_name], $normalizer_name))
      {
        $this->db->sql_insert('sys_full_text_index',
          array(
            'id' => null,
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

  static public function remove($site_object)
  {
    full_text_indexer :: instance()->_do_remove($site_object);
  }

  protected function _do_remove($site_object)
  {
    $this->db->sql_delete('sys_full_text_index', array('object_id' => $site_object->get_id()));
  }

  protected function _normalize_string($content, $normalizer_name)
  {
    $text_normalizer = search_text_normalizer_factory :: create($normalizer_name);

    return $text_normalizer->process($content);
  }
}

?>