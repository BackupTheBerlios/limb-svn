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
class DatasourceTagInfo
{
  var $tag = 'datasource';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'datasource_tag';
}

registerTag(new DatasourceTagInfo());

class DatasourceTag extends ServerComponentTag
{
  function DatasourceTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/datasource_component';
  }

  function preParse()
  {
    if (!isset($this->attributes['target']))
    {
      return throw(new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'target',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    if (!isset($this->attributes['datasource_path']))
    {
      return throw(new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'datasource_path',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    $this->_checkOrderParameter();

    return PARSER_REQUIRE_PARSING;
  }

  function _checkOrderParameter()
  {
    if (!isset($this->attributes['order']))
      return;

    $order_items = explode(',', $this->attributes['order']);
    $order_pairs = array();
    foreach($order_items as $order_pair)
    {
      $arr = explode('=', $order_pair);

      if(!isset($arr[1]))
        continue;

      if(strtolower($arr[1]) != 'asc' &&  strtolower($arr[1]) != 'desc' &&  !strtolower($arr[1]) == 'rand()')
        return throw(new WactException('wrong order type',
          array('tag' => $this->tag,
          'order_value' => $arr[1],
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
  }
  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->set_datasource_path("' . $this->attributes['datasource_path'] .'");');

    if(isset($this->attributes['navigator']))
    {
      $code->writePhp($this->getComponentRefCode() . '->setup_navigator("' . $this->attributes['navigator'] .'");');
    }

    $code->writePhp($this->getComponentRefCode() . '->setup_targets("' . $this->attributes['target'] .'");');
  }

}

?>