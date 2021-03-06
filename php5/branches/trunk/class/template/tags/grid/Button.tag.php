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
require_once(LIMB_DIR . '/class/template/tags/form/Button.tag.php');

class GridButtonTagInfo
{
  public $tag = 'grid:BUTTON';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'grid_button_tag';
}

registerTag(new GridButtonTagInfo());

class GridButtonTag extends ButtonTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/grid_button_component';
  }

  public function checkNestingLevel()
  {
    if (!$this->findParentByClass('grid_list_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:LIST',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function prepare()
  {
    $grid_tag = $this->findParentByClass('grid_list_tag');
    $grid_tag->setFormRequired();

    $this->attributes['type'] = 'button';

    $this->attributes['onclick'] = '';

    if(isset($this->attributes['form_submitted']) &&  (boolean)$this->attributes['form_submitted'])
    {
      $this->attributes['onclick'] .= "add_form_hidden_parameter(this.form, 'grid_form[submitted]', 1);";
    unset($this->attributes['form_submitted']);
    }

    parent :: prepare();
  }

  public function getRenderedTag()
  {
    return 'input';
  }
}

?>