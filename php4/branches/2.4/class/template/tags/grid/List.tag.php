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
  var $tag = 'grid:LIST';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'grid_list_tag';
}

registerTag(new GridListTagInfo());

/**
* The parent compile time component for lists
*/
class GridListTag extends ServerComponentTag
{
  var $has_form = false;

  function GridListTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/list_component';
  }

  function preGenerate($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->prepare();');

    parent :: preGenerate($code);

    if ($this->has_form)
    {
      $code->writeHtml('<form name="grid_form" id="grid_form_'. $this->getServerId() .'" method="post">');
    }

    $code->writePhp('if (' . $this->getDataspaceRefCode() . '->get_total_row_count()){');
  }

  function postGenerate($code)
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

  function getDataspace()
  {
    return $this;
  }

  function getDataspaceRefCode()
  {
    return $this->getComponentRefCode() . '->dataset';
  }

  function setFormRequired($status=true)
  {
    $this->has_form = $status;
  }
}

?>