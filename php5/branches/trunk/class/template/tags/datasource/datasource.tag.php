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
class datasource_tag_info
{
  public $tag = 'datasource';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'datasource_tag';
}

register_tag(new datasource_tag_info());

class datasource_tag extends server_component_tag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/datasource_component';
  }

  public function pre_parse()
  {
    if (!isset($this->attributes['target']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'target',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!isset($this->attributes['datasource_path']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'datasource_path',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    $this->_check_order_parameter();

    return PARSER_REQUIRE_PARSING;
  }

  protected function _check_order_parameter()
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

      if(strtolower($arr[1]) != 'asc' && strtolower($arr[1]) != 'desc'
         && !strtolower($arr[1]) == 'rand()')
        throw new WactException('wrong order type',
          array('tag' => $this->tag,
          'order_value' => $arr[1],
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
  public function generate_contents($code)
  {
    parent :: generate_contents($code);

    $code->write_php($this->get_component_ref_code() . '->set_datasource_path("' . $this->attributes['datasource_path'] .'");');

    if(isset($this->attributes['navigator']))
    {
      $code->write_php($this->get_component_ref_code() . '->setup_navigator("' . $this->attributes['navigator'] .'");');
    }

    $code->write_php($this->get_component_ref_code() . '->setup_targets("' . $this->attributes['target'] .'");');
  }

}

?>