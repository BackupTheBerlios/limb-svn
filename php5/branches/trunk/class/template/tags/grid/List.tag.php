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
class GridListTagInfo
{
  public $tag = 'grid:LIST';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'grid_list_tag';
}

registerTag(new GridListTagInfo());

/**
* The parent compile time component for lists
*/
class GridListTag extends ServerComponentTag
{
  protected $has_form = false;

  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/list_component';
  }

  public function preGenerate($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->prepare();');

    parent :: preGenerate($code);

    if ($this->has_form)
    {
      $code->writeHtml('<form name="grid_form" id="grid_form_'. $this->getServerId() .'" method="post">');
    }

    $code->writePhp('if (' . $this->getDataspaceRefCode() . '->get_total_row_count()){');
  }

  public function postGenerate($code)
  {
    $code->writePhp('} else {');

    if ($default = $this->findImmediateChildByClass('grid_default_tag'))
      $default->generateNow($code);

    $code->writePhp('}');

    if ($this->has_form)
    {
      $code->writeHtml('</form>');
    }

    parent :: postGenerate($code);
  }

  public function getDataspace()
  {
    return $this;
  }

  public function getDataspaceRefCode()
  {
    return $this->getComponentRefCode() . '->dataset';
  }

  public function setFormRequired($status=true)
  {
    $this->has_form = $status;
  }
}

?>