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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(dirname(__FILE__) . '/../normalizers/SearchTextNormalizerFactory.class.php');

class FullTextIndexer
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
      self :: $instance = new FullTextIndexer();

    return self :: $instance;
  }

  static public function add($site_object)
  {
    FullTextIndexer :: instance()->_doAdd($site_object);
  }

  protected function _doAdd($site_object)
  {
    $this->remove($site_object);

    $attributes = $site_object->export();

    reset($attributes);
    $keys = array_keys($attributes);

    foreach($keys as $attribute_name)
    {
      $definition = $site_object->getBehaviour()->getDefinition($attribute_name);

      if (!isset($definition['search']) ||  !$definition['search'])
        continue;

      $weight = isset($definition['search_weight']) ? $definition['search_weight'] : 1;

      $normalizer_name = isset($definition['search_text_normalizer'])
                          ? $definition['search_text_normalizer']
                          : 'search_text_normalizer';

      if($text = $this->_normalizeString($attributes[$attribute_name], $normalizer_name))
      {
        $this->db->sqlInsert('sys_full_text_index',
          array(
            'id' => null,
            'body' => $text,
            'attribute' => $attribute_name,
            'weight' => $weight,
            'object_id' => $site_object->getId(),
            'class_id' => $site_object->getClassId()
          )
        );
      }
    }
  }

  static public function remove($site_object)
  {
    FullTextIndexer :: instance()->_doRemove($site_object);
  }

  protected function _doRemove($site_object)
  {
    $this->db->sqlDelete('sys_full_text_index', array('object_id' => $site_object->getId()));
  }

  protected function _normalizeString($content, $normalizer_name)
  {
    $text_normalizer = SearchTextNormalizerFactory :: create($normalizer_name);

    return $text_normalizer->process($content);
  }
}

?>