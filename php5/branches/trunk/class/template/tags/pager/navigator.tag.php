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
class pager_navigator_tag_info
{
  public $tag = 'pager:NAVIGATOR';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'pager_navigator_tag';
}

register_tag(new pager_navigator_tag_info());

/**
* Compile time component for root of a pager tag
*/
class pager_navigator_tag extends server_component_tag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/pager_component';
  }

  public function pre_generate($code)
  {
    parent::pre_generate($code);

    $code->write_php($this->get_component_ref_code() . '->prepare();');
  }

  public function generate_constructor($code)
  {
    parent::generate_constructor($code);

    if (array_key_exists('items', $this->attributes))
    {
      $code->write_php($this->get_component_ref_code() . '->items = \'' . $this->attributes['items'] . '\';');
    unset($this->attributes['items']);
    }
    if (array_key_exists('pages_per_section', $this->attributes))
    {
      $code->write_php($this->get_component_ref_code() . '->pages_per_section = \'' . $this->attributes['pages_per_section'] . '\';');
    unset($this->attributes['pages_per_section']);
    }
  }

  public function get_component_ref_code()
  {
    if (isset($this->attributes['mirror_of']))
    {
      if($mirrored_pager = $this->parent->find_child($this->attributes['mirror_of']))
        return $mirrored_pager->get_component_ref_code();
      else
        throw new WactException('mirrowed component for pager not found',
          array('tag' => $this->tag,
          'mirror_of' => $this->attributes['mirror_of'],
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    else
      return parent :: get_component_ref_code();
  }
}

?>